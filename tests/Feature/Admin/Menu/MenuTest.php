<?php

namespace Admin\Menu;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Section $section;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->section = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $this->user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'items_manage' => true,
                ],
            ],
        ]);

        $this->actingAs($this->user);
    }

    public function test_create_item()
    {
        $response = $this->post("/admin/restaurants/{$this->restaurant->id}/sections/{$this->section->id}/items", [
            'price' => 10,
            'translations' => [
                'de' => [
                    'title' => 'Pizza',
                    'description' => 'Test desc',
                ],
            ],
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'section_id' => $this->section->id,
        ]);
    }
}
