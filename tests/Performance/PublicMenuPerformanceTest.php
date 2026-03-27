<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\MenuPlan;
use App\Models\Section;
use App\Models\Item;
use App\Models\ItemTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicMenuPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function seedMenu($restaurant, int $items = 50)
    {
        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
        ]);

        for ($i = 0; $i < $items; $i++) {
            $item = Item::factory()->create([
                'section_id' => $section->id,
                'is_active' => true,
                'price' => rand(5, 20),
            ]);

            ItemTranslation::factory()->create([
                'item_id' => $item->id,
                'locale' => 'de',
                'title' => 'Dish ' . $i,
            ]);
        }
    }

    public function test_public_menu_response_time_is_fast(): void
    {
        $plan = MenuPlan::factory()->create([
            'key' => 'pro',
        ]);

        $restaurant = Restaurant::factory()->create([
            'slug' => 'perf-test',
            'plan_key' => $plan->key,
        ]);

        $this->seedMenu($restaurant, 80);

        $start = microtime(true);

        $response = $this->get('/r/perf-test');

        $duration = microtime(true) - $start;

        $response->assertStatus(200);

        // 👉 целевое время (настрой под себя)
        $this->assertLessThan(0.8, $duration, "Response too slow: {$duration}s");
    }
}
