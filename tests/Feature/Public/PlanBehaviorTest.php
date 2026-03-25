<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Section;
use App\Models\Restaurant;
use App\Models\MenuPlan;
use App\Models\ItemTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanBehaviorTest extends TestCase
{
    use RefreshDatabase;

    public function test_starter_has_no_images(): void
    {
        $plan = MenuPlan::factory()->create([
            'key' => 'starter',
            'features' => [
                'images' => false,
                'item_modal' => false,
                'spicy' => false,
                'is_new' => false,
                'dish_of_day' => false,
                'long_description' => false,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'slug' => 'starter-test',
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
                'spicy' => 3,
                'is_new' => true,
                'dish_of_day' => true,
            ],
        ]);

        ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Pizza Test',
            'description' => 'Kurz',
            'details' => 'Lang',
        ]);

        $response = $this->get('/r/starter-test');

        $response->assertStatus(200);
        $response->assertDontSee('menu-item-image');
        $response->assertDontSee('data-open-modal');
        $response->assertDontSee('NEW');
    }

    public function test_basic_has_modal_and_spicy_but_no_new_and_no_dish_of_day(): void
    {
        $plan = MenuPlan::factory()->create([
            'key' => 'basic',
            'features' => [
                'images' => true,
                'item_modal' => true,
                'spicy' => true,
                'is_new' => false,
                'dish_of_day' => false,
                'long_description' => false,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'slug' => 'basic-test',
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
            'title' => 'Basic Item',
            'description' => 'Short desc',
            'details' => 'Long desc',
        ]);

        $response = $this->get('/r/basic-test');

        $response->assertStatus(200);
        $response->assertSee('data-open-modal', false);
        $response->assertDontSee('NEW');
        $response->assertDontSee('Dish of Day');
    }
}
