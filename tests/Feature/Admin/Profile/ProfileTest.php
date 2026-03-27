<?php

namespace Admin\Profile;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
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

    public function test_update_restaurant_name()
    {
        $response = $this->post("/admin/restaurants/{$this->restaurant->id}/profile", [
            'restaurant_name' => 'NEW NAME',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'name' => 'NEW NAME',
        ]);
    }

    public function test_update_profile()
    {
        $this->post("/admin/restaurants/{$this->restaurant->id}/profile", [
            'restaurant_name' => 'NEW',
            'city' => 'Berlin',
        ]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'name' => 'NEW',
            'city' => 'Berlin',
        ]);
    }

    public function test_profile_validation()
    {
        $this->post("/admin/restaurants/{$this->restaurant->id}/profile", [
            'restaurant_name' => '',
        ])->assertSessionHasErrors(['restaurant_name']);
    }
}
