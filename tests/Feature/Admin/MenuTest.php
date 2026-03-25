<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Section;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_item()
    {
        $restaurant = Restaurant::factory()->create();

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'meta' => [
                'permissions' => [
                    'items_manage' => true, // 🔥 ВАЖНО
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post("/admin/restaurants/{$restaurant->id}/sections/{$section->id}/items", [
            'price' => 10,
            'translations' => [
                'de' => [
                    'title' => 'Pizza',
                    'description' => 'Test desc',
                ],
            ],
        ]);

        $response->assertStatus(302); // Laravel redirect

        $this->assertDatabaseHas('items', [
            'section_id' => $section->id,
        ]);
    }
}
