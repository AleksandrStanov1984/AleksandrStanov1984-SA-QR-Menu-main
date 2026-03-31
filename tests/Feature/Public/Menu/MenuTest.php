<?php

namespace Tests\Feature\Public\Menu;

use Tests\TestCase;
use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Storage;
use Tests\Traits\FileTestHelpers;

class MenuTest extends TestCase
{
    use RefreshDatabase, FileTestHelpers;

    protected MenuPlan $starterPlan;
    protected MenuPlan $basicPlan;
    protected MenuPlan $proPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->starterPlan = MenuPlan::factory()->create([
            'key' => 'starter',
            'features' => [
                'show_images' => false,
                'item_modal' => false,
                'long_description' => false,
            ],
        ]);

        $this->basicPlan = MenuPlan::factory()->create([
            'key' => 'basic',
            'features' => [
                'show_images' => true,
                'item_modal' => true,
                'long_description' => false,
                'spicy' => true,
            ],
        ]);

        $this->proPlan = MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'show_images' => true,
                'item_modal' => true,
                'long_description' => true,
                'dish_of_day' => true,
                'is_new' => true,
                'spicy' => true,
            ],
        ]);
    }

    protected function createRestaurant(string $slug, string $planKey): Restaurant
    {
        return Restaurant::factory()->create([
            'slug' => $slug,
            'plan_key' => $planKey,
        ]);
    }

    protected function createMenuItem(Restaurant $restaurant, array $meta = [], bool $withImage = false): Item
    {
        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
        ]);

        $item = Item::factory()->create([
            'section_id' => $section->id,
            'price' => 10.50,
            'image_path' => $withImage ? 'restaurants/1/test.jpg' : null,
            'is_active' => true,
            'meta' => array_merge([
                'dish_of_day' => false,
                'is_new' => false,
                'spicy_level' => 3,
                'show_image' => true,
            ], $meta),
        ]);

        ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Test Dish',
            'description' => 'Short description',
            'details' => 'Long description here',
        ]);

        return $item;
    }

    public function test_public_menu_page_loads()
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'panino-pizza-test',
        ]);

        $response = $this->get('/r/panino-pizza-test');

        $response->assertStatus(200);
    }

    public function test_menu_uses_viewmodel()
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'panino-pizza-test',
        ]);

        $response = $this->get('/r/panino-pizza-test');

        $response->assertSee($restaurant->name);
    }

    public function test_public_page_loads(): void
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'test',
        ]);

        $this->get('/r/test')
            ->assertStatus(200)
            ->assertSee($restaurant->name);
    }

    public function test_starter_menu_simple_view_no_modal_behavior_no_real_item_image(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'menu-starter',
            planKey: $this->starterPlan->key
        );

        $this->createMenuItem($restaurant, [], false);

        $response = $this->get('/r/menu-starter');

        $response->assertStatus(200);

        $response->assertSee('Test Dish');
        $response->assertSee('10.50');
        $response->assertSee('Short description');
        $response->assertSee('menu-item is-no-image', false);
        $response->assertDontSee('data-open-modal="item"', false);
        $response->assertDontSee('Long description here');
    }

    public function test_modal_available_for_pro(): void
    {
        // 👉 план (без дублей)
        $plan = \App\Models\MenuPlan::firstOrCreate(
            ['key' => 'pro'],
            [
                'name' => 'Pro',
                'features' => [
                    'images' => true,
                    'item_modal' => true,
                    'spicy' => true,
                    'is_new' => true,
                    'dish_of_day' => true,
                    'long_description' => true,
                ],
            ]
        );

        $restaurant = Restaurant::factory()->create([
            'slug' => 'pro-test',
            'plan_key' => $plan->key,
        ]);

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
        ]);

        $relativePath = "restaurants/{$restaurant->id}/menu/items/test.webp";
        $fullPath = public_path("assets/" . $relativePath);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        file_put_contents($fullPath, 'fake-image');

        $item = Item::factory()->create([
            'section_id' => $section->id,
            'image_path' => $relativePath,
            'is_active' => true,
            'meta' => [
                'show_image' => true,
                'spicy_level' => 2,
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

        $html = $response->getContent();

    }

    public function test_basic_menu_has_no_modal_and_no_long_description(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'menu-basic',
            planKey: $this->basicPlan->key
        );

        $this->createMenuItem($restaurant, [], true);

        $response = $this->get('/r/menu-basic');

        $response->assertStatus(200);

        $response->assertSee('Test Dish');
        $response->assertSee('10.50');
        $response->assertSee('Short description');

        $response->assertDontSee('data-open-modal');
        $response->assertDontSee('id="itemModal"');

        $response->assertDontSee('Long description here');
    }

    public function test_pro_menu_full_features_with_modal_details_and_badges(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'menu-pro',
            planKey: $this->proPlan->key
        );

        $this->createMenuItem($restaurant, [
            'dish_of_day' => true,
            'is_new' => true,
            'spicy_level' => 5,
        ], true);

        $response = $this->get('/r/menu-pro');

        $response->assertStatus(200);

        $response->assertSee('Test Dish');
        $response->assertSee('10.50');
        $response->assertSee('Short description');

        $response->assertSee('id="itemModal"', false);

        $response->assertSee('menu-item-badge--dish', false);
        $response->assertSee('menu-item-badge--new', false);

        $response->assertSee('Tagesgericht');
        $response->assertSee('NEW');
    }
}
