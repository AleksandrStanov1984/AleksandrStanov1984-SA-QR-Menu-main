<?php

namespace Admin\Hours;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminHelpers;

class HoursTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    protected Restaurant $restaurant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->user = $this->admin($this->restaurant, [
            'restaurants.edit' => true,
        ]);

        $this->actingAs($this->user);
    }

    public function test_hours_saved_correctly()
    {
        $response = $this->post("/admin/restaurants/{$this->restaurant->id}/hours", [
            'hours' => [
                1 => [
                    'open_time' => '10:00',
                    'close_time' => '18:00',
                ],
            ],
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('restaurant_hours', [
            'restaurant_id' => $this->restaurant->id,
            'day_of_week' => 1,
            'open_time' => '10:00',
            'close_time' => '18:00',
            'is_closed' => 0,
        ]);
    }

    public function test_hours_saved(): void
    {
        $this->post("/admin/restaurants/{$this->restaurant->id}/hours", [
            'hours' => [
                1 => [
                    'open_time' => '10:00',
                    'close_time' => '22:00',
                ],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('restaurant_hours', [
            'restaurant_id' => $this->restaurant->id,
            'day_of_week' => 1,
            'open_time' => '10:00',
            'close_time' => '22:00',
            'is_closed' => 0,
        ]);
    }

    public function test_hours_validation_fails_on_invalid_time(): void
    {
        $this->post("/admin/restaurants/{$this->restaurant->id}/hours", [
            'hours' => [
                1 => [
                    'open_time' => '10:15',
                    'close_time' => '22:00',
                ],
            ],
        ])->assertSessionHasErrors(['hours.1.open_time']);
    }
}
