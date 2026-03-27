<?php

namespace Tests\Feature\Admin\Socials;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantSocialLink;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SocialLinksTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'socials.add.3' => true,
                    'socials.add.4' => true,
                    'socials.add.5' => true,
                    'socials.edit' => true,
                    'socials.delete' => true,
                    'socials.toggle.active' => true,
                    'socials.icon.upload' => true,
                ],
            ],
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function social_link_can_be_created_with_valid_data()
    {
        $this->post(route('admin.restaurants.social_links.store', $this->restaurant), [
            'title' => 'Instagram',
            'url' => 'https://instagram.com/test',
        ]);

        $this->assertDatabaseHas('restaurant_social_links', [
            'restaurant_id' => $this->restaurant->id,
            'title' => 'Instagram',
        ]);
    }

    /** @test */
    public function url_must_be_valid_https()
    {
        $response = $this->post(route('admin.restaurants.social_links.store', $this->restaurant), [
            'title' => 'Instagram',
            'url' => 'instagram.com/test',
        ]);

        $response->assertSessionHasErrors('url');
    }

    /** @test */
    public function uploaded_icon_is_saved_and_used()
    {
        $file = UploadedFile::fake()->createWithContent(
            'icon.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"></svg>'
        );

        $response = $this->post(route('admin.restaurants.social_links.store', $this->restaurant), [
            'title' => 'Instagram',
            'url' => 'https://instagram.com/test',
            'icon' => $file,
        ]);

        $response->assertStatus(302);

        $link = RestaurantSocialLink::first();

        $this->assertNotNull($link);
        $this->assertNotNull($link->icon_path);
    }

    /** @test */
    public function social_link_can_be_disabled()
{
    $link = RestaurantSocialLink::create([
        'restaurant_id' => $this->restaurant->id,
        'title' => 'Instagram',
        'url' => 'https://instagram.com/test',
        'is_active' => true,
    ]);

    $response = $this->patch(route('admin.restaurants.social_links.toggle_active', [
        $this->restaurant,
        $link
    ]));

    $response->assertStatus(302);

    $link->refresh();

    $this->assertFalse($link->is_active);
}

    /** @test */
    public function social_link_can_be_updated()
    {
        $link = RestaurantSocialLink::create([
            'restaurant_id' => $this->restaurant->id,
            'title' => 'Instagram',
            'url' => 'https://instagram.com/test',
        ]);

        $this->put(route('admin.restaurants.social_links.update', [
            $this->restaurant,
            $link
        ]), [
            'title' => 'Instagram Updated',
            'url' => 'https://instagram.com/new',
        ]);

        $link->refresh();

        $this->assertEquals('Instagram Updated', $link->title);
        $this->assertEquals('https://instagram.com/new', $link->url);
    }
}
