<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Item;
use App\Models\User;
use App\Models\Section;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemActiveTest extends TestCase
{
    use RefreshDatabase;
    use AdminHelpers;

    public function test_toggle_item_active(): void
    {
        $restaurant = Restaurant::factory()->create();

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $item = Item::factory()->create([
            'section_id' => $section->id,
            'is_active' => true,
        ]);

        $user = $this->admin($restaurant, [
            'items_manage' => true,
        ]);

        $this->actingAs($user)
            ->patch("/admin/restaurants/{$restaurant->id}/items/{$item->id}/active", [
                'is_active' => 0,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_active' => 0,
        ]);
    }
}
