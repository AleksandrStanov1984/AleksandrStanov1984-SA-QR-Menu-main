<?php

namespace Tests\Feature\Permissions;

use App\Models\Section;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->section = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    public function test_user_without_permission_cannot_access_items()
    {
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'is_super_admin' => false,
            'meta' => [
                'permissions' => [
                    'items_manage' => false,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post(
            "/admin/restaurants/{$this->restaurant->id}/sections/{$this->section->id}/items"
        );

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_everything()
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertStatus(200);
    }
}
