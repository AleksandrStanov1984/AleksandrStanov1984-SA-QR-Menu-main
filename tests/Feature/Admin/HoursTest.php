<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HoursTest extends TestCase
{
    use RefreshDatabase;

    public function test_hours_saved_correctly()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurants.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post("/admin/restaurants/{$restaurant->id}/hours", [
            'hours' => [
                1 => [
                    'open_time' => '10:00',
                    'close_time' => '18:00',
                ],
            ],
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('restaurant_hours', [
            'restaurant_id' => $restaurant->id,
            'day_of_week' => 1,
            'open_time' => '10:00',
            'close_time' => '18:00',
            'is_closed' => 0,
        ]);
    }
}
