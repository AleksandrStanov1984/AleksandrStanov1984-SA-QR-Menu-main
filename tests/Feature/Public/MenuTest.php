<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_menu_page_loads()
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'panino-pizza-test',
        ]);

        $response = $this->get("/r/panino-pizza-test");

        $response->assertStatus(200);
    }

    public function test_menu_uses_viewmodel()
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'panino-pizza-test',
        ]);

        $response = $this->get("/r/panino-pizza-test");

        $response->assertSee($restaurant->name);
    }
}
