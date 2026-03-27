<?php

namespace Tests\Feature\Public\Header;

use Tests\TestCase;
use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HeaderTest extends TestCase
{
    use RefreshDatabase;

    protected MenuPlan $starterPlan;
    protected MenuPlan $basicPlan;
    protected MenuPlan $proPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->starterPlan = MenuPlan::factory()->create([
            'key' => 'starter',
            'features' => [
                'dish_of_day' => false,
            ],
        ]);

        $this->basicPlan = MenuPlan::factory()->create([
            'key' => 'basic',
            'features' => [
                'dish_of_day' => false,
            ],
        ]);

        $this->proPlan = MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'dish_of_day' => true,
            ],
        ]);
    }

    protected function createRestaurant(string $slug, string $name, string $planKey): Restaurant
    {
        return Restaurant::factory()->create([
            'slug' => $slug,
            'name' => $name,
            'plan_key' => $planKey,
        ]);
    }

    protected function createDishOfDayItem(Restaurant $restaurant, string $title = 'Dish Day'): Item
    {
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
            'title' => $title,
        ]);

        return $item;
    }

    public function test_header_contains_basic_elements(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'header-basic',
            name: 'Test Restaurant',
            planKey: $this->basicPlan->key
        );

        $response = $this->get('/r/header-basic');

        $response->assertStatus(200);

        $response->assertSee('id="drawerOpen"', false);
        $response->assertSee('class="drawer-btn"', false);

        $response->assertSee('Test Restaurant');
    }

    public function test_header_carousel_not_visible_for_non_pro(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'header-starter',
            name: 'Starter Header',
            planKey: $this->starterPlan->key
        );

        $this->createDishOfDayItem($restaurant);

        $response = $this->get('/r/header-starter');

        $response->assertStatus(200);

        $response->assertDontSee('header-carousel', false);
    }

    public function test_header_carousel_visible_for_pro(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'header-pro',
            name: 'Pro Header',
            planKey: $this->proPlan->key
        );

        $this->createDishOfDayItem($restaurant);

        $response = $this->get('/r/header-pro');

        $response->assertStatus(200);

        $response->assertSee('Pro Header');
        $response->assertSee('Dish Day');
        $response->assertSee('Tagesgericht');
        $response->assertSee('menu-item-badge--dish', false);
    }
}
