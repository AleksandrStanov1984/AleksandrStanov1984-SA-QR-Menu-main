<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Section;
use App\Models\Restaurant;
use App\Models\MenuPlan;
use App\Models\ItemTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_only_for_pro(): void
    {
        $starter = MenuPlan::factory()->create([
            'key' => 'starter',
            'features' => [
                'dish_of_day' => false,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'slug' => 'footer-starter',
            'plan_key' => $starter->key,
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
                'dish_of_day' => true,
                'show_image' => true,
            ],
        ]);

        ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Dish Day',
            'description' => 'Short',
        ]);

        $this->get('/r/footer-starter')
            ->assertStatus(200)
            ->assertDontSee('footer-gallery');
    }

    public function test_footer_visible_for_pro(): void
    {
        $pro = MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'dish_of_day' => true,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'slug' => 'footer-pro',
            'plan_key' => $pro->key,
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
                'dish_of_day' => true,
                'show_image' => true,
            ],
        ]);

        ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Dish Day',
            'description' => 'Short',
        ]);

        $this->get('/r/footer-pro')
            ->assertStatus(200)
            ->assertSee('footer-gallery');
    }
}
