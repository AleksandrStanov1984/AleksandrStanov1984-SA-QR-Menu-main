<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;
    use AdminHelpers;

    public function test_no_permission_returns_403(): void
    {
        $restaurant = Restaurant::factory()->create();
        $user = $this->admin($restaurant, []);

        $this->actingAs($user)
            ->post("/admin/restaurants/{$restaurant->id}/sections", [
                'title' => 'Test Category',
            ])
            ->assertStatus(403);
    }

    public function test_user_with_sections_permission_can_create_category(): void
    {
        $restaurant = Restaurant::factory()->create();
        $user = $this->admin($restaurant, [
            'sections_manage' => true,
        ]);

        $this->actingAs($user)
            ->post("/admin/restaurants/{$restaurant->id}/sections", [
                'title' => 'Pizza',
            ]);

        $this->assertDatabaseHas('sections', [
            'restaurant_id' => $restaurant->id,
        ]);
    }
}
