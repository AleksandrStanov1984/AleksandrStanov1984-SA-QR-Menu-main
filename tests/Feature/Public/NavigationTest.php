<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Section;
use App\Models\Restaurant;
use App\Models\SectionTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_visible(): void
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'nav-test',
        ]);

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SectionTranslation::factory()->create([
            'section_id' => $section->id,
            'locale' => 'de',
            'title' => 'Pizza',
        ]);

        $response = $this->get('/r/nav-test');

        $response->assertStatus(200);
        $response->assertSee('Pizza');
    }
}
