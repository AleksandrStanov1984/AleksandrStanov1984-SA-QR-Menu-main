<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HoursAllTest extends TestCase
{
    use RefreshDatabase;
    use AdminHelpers;

    public function test_hours_saved(): void
    {
        $restaurant = Restaurant::factory()->create();

        $user = $this->admin($restaurant, [
            'restaurants.edit' => true,
        ]);

        $this->actingAs($user)
            ->post("/admin/restaurants/{$restaurant->id}/hours", [
                'hours' => [
                    1 => [
                        'open_time' => '10:00',
                        'close_time' => '22:00',
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('restaurant_hours', [
            'restaurant_id' => $restaurant->id,
            'day_of_week' => 1,
            'open_time' => '10:00',
            'close_time' => '22:00',
            'is_closed' => 0,
        ]);
    }

    public function test_hours_validation_fails_on_invalid_time(): void
    {
        $restaurant = Restaurant::factory()->create();

        $user = $this->admin($restaurant, [
            'restaurants.edit' => true,
        ]);

        $this->actingAs($user)
            ->post("/admin/restaurants/{$restaurant->id}/hours", [
                'hours' => [
                    1 => [
                        'open_time' => '10:15',
                        'close_time' => '22:00',
                    ],
                ],
            ])
            ->assertSessionHasErrors(['hours.1.open_time']);
    }
}
