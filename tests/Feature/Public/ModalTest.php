<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Section;
use App\Models\Restaurant;
use App\Models\MenuPlan;
use App\Models\ItemTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_modal_available_for_pro(): void
    {
        $plan = MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'images' => true,
                'item_modal' => true,
                'spicy' => true,
                'is_new' => true,
                'dish_of_day' => true,
                'long_description' => true,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'slug' => 'pro-test',
            'plan_key' => $plan->key,
        ]);

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
        ]);

        $item = Item::factory()->create([
            'section_id' => $section->id,
            'image_path' => 'restaurants/1/test.jpg',
            'is_active' => true,
            'meta' => [
                'show_image' => true,
                'spicy' => 2,
                'is_new' => true,
                'dish_of_day' => true,
            ],
        ]);

        ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Pro Item',
            'description' => 'Short',
            'details' => 'Long details here',
        ]);

        $response = $this->get('/r/pro-test');

        $response->assertStatus(200);
        $response->assertSee('data-open-modal', false);
        $response->assertSee('data-details=', false);
    }
}
