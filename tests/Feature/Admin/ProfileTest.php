<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_restaurant_name()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurant.profile.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post("/admin/restaurants/{$restaurant->id}/profile", [
            'restaurant_name' => 'NEW NAME',
        ]);

        $response->assertStatus(302); // redirect back

        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => 'NEW NAME',
        ]);
    }
}
