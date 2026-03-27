<?php

namespace Tests\Feature\Admin\Profile;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileMainTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurant.profile.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function profile_can_be_updated()
    {
        // ❗ свой restaurant с начальными данными (не трогаем setUp)
        $restaurant = Restaurant::factory()->create([
            'name' => 'Old Name',
            'contact_name' => 'Old Contact',
            'phone' => '+4900123456',
            'city' => 'Old City',
            'postal_code' => '00000',
            'street' => 'Old Street',
            'house_number' => '1',
        ]);

        $this->user->update([
            'restaurant_id' => $restaurant->id,
        ]);

        $response = $this->post(route('admin.restaurants.profile.update', $restaurant), [
            'restaurant_name' => 'New Restaurant',
            'contact_name' => 'New Contact',
            'phone' => '999999',
            'contact_email' => 'test@example.com',
            'city' => 'Berlin',
            'postal_code' => '10115',
            'street' => 'Alexanderplatz',
            'house_number' => '10',
        ]);

        $response->assertStatus(302);

        $restaurant->refresh();

        $this->assertEquals('New Restaurant', $restaurant->name);
        $this->assertEquals('New Contact', $restaurant->contact_name);
        $this->assertEquals('999999', $restaurant->phone);
        $this->assertEquals('test@example.com', $restaurant->contact_email);
        $this->assertEquals('Berlin', $restaurant->city);
        $this->assertEquals('10115', $restaurant->postal_code);
        $this->assertEquals('Alexanderplatz', $restaurant->street);
        $this->assertEquals('10', $restaurant->house_number);
    }

    /** @test */
    public function profile_validation_fails_with_invalid_data()
    {
        $response = $this->from(route('admin.restaurants.profile', $this->restaurant))
            ->post(route('admin.restaurants.profile.update', $this->restaurant), [
                'restaurant_name' => '',
                'contact_name' => str_repeat('a', 300),
                'phone' => '',
                'contact_email' => 'not-an-email',
                'city' => '',
                'street' => '',
                'house_number' => '',
                'postal_code' => '',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'restaurant_name',
            'contact_name',
            'contact_email',
        ]);
    }

    /** @test */
    public function user_cannot_change_plan_or_template_from_profile()
    {
        // ❗ отдельный restaurant с планом
        $restaurant = Restaurant::factory()->create([
            'name' => 'Test Restaurant',
            'plan_key' => 'starter',
            'template_key' => 'united',
        ]);

        $this->user->update([
            'restaurant_id' => $restaurant->id,
            'is_super_admin' => false,
        ]);

        $response = $this->post(route('admin.restaurants.profile.update', $restaurant), [
            'restaurant_name' => 'Updated Restaurant',
            'contact_name' => 'User Contact',
            'phone' => '123456',
            'contact_email' => 'user@example.com',
            'city' => 'Berlin',
            'street' => 'Alexanderplatz',
            'house_number' => '10',
            'postal_code' => '10115',
        ]);

        $response->assertStatus(302);

        $restaurant->refresh();

        $this->assertEquals('starter', $restaurant->plan_key);
        $this->assertEquals('united', $restaurant->template_key);

        $this->assertEquals('Updated Restaurant', $restaurant->name);
    }
}
