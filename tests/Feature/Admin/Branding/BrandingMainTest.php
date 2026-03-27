<?php

namespace Tests\Feature\Admin\Branding;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class BrandingMainTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->restaurant = Restaurant::factory()->create([
            'meta' => [],
        ]);
    }

    /** @test */
    public function branding_page_exists()
    {
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'branding.backgrounds.upload' => true,
                    'branding.theme_mode.edit' => true,
                ]
            ]
        ]);

        $this->actingAs($user);

        $this->get(route('admin.restaurants.branding', $this->restaurant))
            ->assertStatus(200);
    }

    /** @test */
    public function theme_mode_can_be_updated()
    {
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'branding.theme_mode.edit' => true,
                ]
            ]
        ]);

        $this->actingAs($user);

        $this->post(
            route('admin.restaurants.branding.backgrounds.update', $this->restaurant),
            [
                'theme_mode' => 'dark',
            ]
        );

        $this->restaurant->refresh();

        $this->assertEquals('dark', $this->restaurant->meta['theme_mode']);
    }

    /** @test */
    public function light_background_can_be_uploaded()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'branding.backgrounds.upload' => true,
                ]
            ]
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('bg-light.jpg');

        $this->post(
            route('admin.restaurants.branding.backgrounds.update', $this->restaurant),
            [
                'bg_light' => $file,
            ]
        );

        $this->restaurant->refresh();

        $this->assertArrayHasKey('bg_light', $this->restaurant->meta);

        $path = $this->restaurant->meta['bg_light'];

        $this->assertNotNull($path);
        $this->assertStringContainsString('branding/backgrounds', $path);
        $this->assertStringEndsWith('.webp', $path);
    }

    /** @test */
    public function dark_background_can_be_uploaded()
    {
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'branding.backgrounds.upload' => true,
                ]
            ]
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('bg-dark.jpg');

        $this->post(
            route('admin.restaurants.branding.backgrounds.update', $this->restaurant),
            [
                'bg_dark' => $file,
            ]
        );

        $this->restaurant->refresh();

        $this->assertArrayHasKey('bg_dark', $this->restaurant->meta);

        $path = $this->restaurant->meta['bg_dark'];

        $this->assertNotEmpty($path);
        $this->assertStringContainsString('branding/backgrounds', $path);
        $this->assertStringEndsWith('.webp', $path);
    }

    /** @test */
    public function background_update_merges_meta_not_overwrites()
    {
        // тут нужен свой restaurant с meta → НЕ используем setUp
        $restaurant = Restaurant::factory()->create([
            'meta' => [
                'bg_light' => 'old.jpg',
            ],
        ]);

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'meta' => [
                'permissions' => [
                    'branding.theme_mode.edit' => true,
                ]
            ]
        ]);

        $this->actingAs($user);

        $this->post(
            route('admin.restaurants.branding.backgrounds.update', $restaurant),
            [
                'theme_mode' => 'light',
            ]
        );

        $restaurant->refresh();

        $this->assertEquals('old.jpg', $restaurant->meta['bg_light']);
        $this->assertEquals('light', $restaurant->meta['theme_mode']);
    }
}
