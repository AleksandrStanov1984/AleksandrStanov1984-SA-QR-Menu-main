<?php

namespace Admin\Permissions;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminHelpers;

class PermissionTest extends TestCase
{
    use RefreshDatabase;
    use AdminHelpers;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
    }

    public function test_no_permission_returns_403(): void
    {
        $user = $this->admin($this->restaurant, []);

        $this->actingAs($user)
            ->post("/admin/restaurants/{$this->restaurant->id}/sections", [
                'title' => 'Test Category',
            ])
            ->assertStatus(403);
    }

    public function test_user_with_sections_permission_can_create_category(): void
    {
        $user = $this->admin($this->restaurant, [
            'sections_manage' => true,
        ]);

        $this->actingAs($user)
            ->post("/admin/restaurants/{$this->restaurant->id}/sections", [
                'title' => 'Pizza',
            ]);

        $this->assertDatabaseHas('sections', [
            'restaurant_id' => $this->restaurant->id,
        ]);
    }
}
