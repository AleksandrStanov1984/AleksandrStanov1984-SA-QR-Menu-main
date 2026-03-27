<?php

namespace Admin\Menu;

use App\Models\Item;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemMetaTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Section $section;
    protected Item $item1;
    protected Item $item2;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->section = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $this->item1 = Item::factory()->create([
            'section_id' => $this->section->id,
            'meta' => ['dish_of_day' => true],
        ]);

        $this->item2 = Item::factory()->create([
            'section_id' => $this->section->id,
            'meta' => ['dish_of_day' => false],
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

    public function test_only_one_dish_of_day_per_section()
    {
        $response = $this->patchJson("/admin/restaurants/{$this->restaurant->id}/items/{$this->item2->id}/meta", [
            'dish_of_day' => true,
        ]);

        $response->assertOk();

        $this->item1->refresh();
        $this->item2->refresh();

        $this->assertFalse((bool) ($this->item1->meta['dish_of_day'] ?? false));
        $this->assertTrue((bool) ($this->item2->meta['dish_of_day'] ?? false));
    }
}
