<?php

namespace Admin\Menu;

use App\Models\Item;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminHelpers;

class ItemActiveTest extends TestCase
{
    use RefreshDatabase;
    use AdminHelpers;

    protected Restaurant $restaurant;
    protected Section $section;
    protected Item $item;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->section = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $this->item = Item::factory()->create([
            'section_id' => $this->section->id,
            'is_active' => true,
        ]);

        $this->user = $this->admin($this->restaurant, [
            'items_manage' => true,
        ]);

        $this->actingAs($this->user);
    }

    public function test_toggle_item_active(): void
    {
        $this->patch("/admin/restaurants/{$this->restaurant->id}/items/{$this->item->id}/active", [
            'is_active' => 0,
        ])->assertStatus(200);

        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'is_active' => 0,
        ]);
    }
}
