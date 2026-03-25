<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Section;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_dish_of_day_per_section()
    {
        $restaurant = Restaurant::factory()->create();

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $item1 = Item::factory()->create([
            'section_id' => $section->id,
            'meta' => ['dish_of_day' => true],
        ]);

        $item2 = Item::factory()->create([
            'section_id' => $section->id,
            'meta' => ['dish_of_day' => false],
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

        $response = $this->patchJson("/admin/restaurants/{$restaurant->id}/items/{$item2->id}/meta", [
            'dish_of_day' => true,
        ]);

        $response->assertOk();

        $item1->refresh();
        $item2->refresh();

        $this->assertFalse((bool) ($item1->meta['dish_of_day'] ?? false));
        $this->assertTrue((bool) ($item2->meta['dish_of_day'] ?? false));
    }
}
