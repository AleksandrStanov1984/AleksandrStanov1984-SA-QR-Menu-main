<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Section;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
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
                    'items_manage' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post("/admin/restaurants/{$restaurant->id}/sections/{$section->id}/items", [
            'price' => 10,
            'translations' => [
                'de' => [
                    'title' => 'Test Item',
                    'description' => 'Test Desc',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('items', [
            'section_id' => $section->id,
        ]);
    }
}
