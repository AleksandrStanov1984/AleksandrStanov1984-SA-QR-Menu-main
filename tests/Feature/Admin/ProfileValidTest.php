<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileValidTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile()
    {
        $r = Restaurant::factory()->create();

        $u = User::factory()->create([
            'restaurant_id' => $r->id,
            'meta' => [
                'permissions' => [
                    'restaurant.profile.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($u);

        $this->post("/admin/restaurants/{$r->id}/profile", [
            'restaurant_name' => 'NEW',
            'city' => 'Berlin',
        ]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $r->id,
            'name' => 'NEW',
            'city' => 'Berlin',
        ]);
    }

    public function test_profile_validation()
    {
        $r = Restaurant::factory()->create();

        $u = User::factory()->create([
            'restaurant_id' => $r->id,
            'meta' => [
                'permissions' => [
                    'restaurant.profile.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($u);

        $this->post("/admin/restaurants/{$r->id}/profile", [
            'restaurant_name' => '',
        ])->assertSessionHasErrors(['restaurant_name']);
    }
}
