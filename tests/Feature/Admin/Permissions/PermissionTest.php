<?php

namespace Admin\Permissions;

use App\Models\Restaurant;
use App\Models\User;
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

    public function test_no_permission_returns_redirect(): void
    {
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'is_super_admin' => false,
            'meta' => [
                'permissions' => []
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post("/admin/restaurants/{$this->restaurant->id}/sections", [
            'title' => 'Test Category',
            'locale' => 'de',
        ]);

        $response->assertStatus(302);

        $response->assertRedirect();
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
