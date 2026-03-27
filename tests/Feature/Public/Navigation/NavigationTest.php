<?php

namespace Public\Navigation;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create([
            'slug' => 'nav-test',
        ]);
    }

    public function test_categories_visible(): void
    {
        $section = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id,
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
