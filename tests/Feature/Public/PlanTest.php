<?php

namespace Tests\Feature\Public;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\MenuPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_starter_plan_has_no_images()
    {
        $plan = MenuPlan::factory()->create([
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

    public function test_pro_plan_has_modal()
    {
        MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'item_modal' => true,
                'images' => true,
                'spicy' => true,
                'is_new' => true,
                'dish_of_day' => true,
                'long_description' => true,
            ],
        ]);

        $restaurant = Restaurant::factory()->create([
            'plan_key' => 'pro',
            'default_locale' => 'de',
        ]);

        $section = \App\Models\Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        \App\Models\SectionTranslation::factory()->create([
            'section_id' => $section->id,
            'locale' => 'de',
            'title' => 'Pizza',
        ]);

        $item = \App\Models\Item::factory()->create([
            'section_id' => $section->id,
            'is_active' => true,
            'price' => 10,
            'meta' => [
                'dish_of_day' => false,
                'is_new' => false,
                'spicy' => 0,
                'show_image' => false,
            ],
        ]);

        \App\Models\ItemTranslation::factory()->create([
            'item_id' => $item->id,
            'locale' => 'de',
            'title' => 'Margherita',
            'description' => 'Test item',
            'details' => 'Long text',
        ]);

        $response = $this->get("/r/{$restaurant->slug}");

        $response->assertOk();
        $response->assertSee('Margherita');
        $response->assertSee('data-open-modal', false);
    }

    public function test_item_image_saved()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        Storage::disk('public')->assertExists('...');
    }
}
