<?php

namespace Public\Plan;

use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_starter_plan_has_no_images()
    {
        MenuPlan::factory()->create([
            'key' => 'starter',
            'features' => [
                'images' => false,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'plan_key' => 'starter',
        ]);

        $response = $this->get("/r/{$restaurant->slug}");

        $response->assertDontSee('menu-item-image');
    }

    public function test_pro_plan_without_image_has_no_modal()
    {
        MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'item_modal' => true,
                'images' => true,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'plan_key' => 'pro',
            'default_locale' => 'de',
        ]);

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
        ]);

        SectionTranslation::factory()->create([
            'section_id' => $section->id,
            'locale' => 'de',
            'title' => 'Pizza',
        ]);

        $item = Item::factory()->create([
            'section_id' => $section->id,
            'is_active' => true,
            'meta' => [
                'show_image' => false,
            ],
        ]);

        ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Margherita',
        ]);

        $response = $this->get("/r/{$restaurant->slug}");

        $response->assertOk();
        $response->assertSee('Margherita');
        $response->assertDontSee('data-open-modal');
    }

    public function test_item_image_saved()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        Storage::disk('public')->assertExists('...');
    }

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
