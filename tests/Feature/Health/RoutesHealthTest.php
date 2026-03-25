<?php

namespace Tests\Feature\Health;

use App\Models\Section;
use App\Models\Restaurant;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesHealthTest extends TestCase
{
    use RefreshDatabase; // 🔥 ВАЖНО

    public function test_main_routes_return_200()
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'test-rest',
        ]);

        Section::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $urls = [
            '/',
            '/admin/login',
            '/r/test-rest',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);

            $this->assertTrue(
                in_array($response->status(), [200, 302]),
                "URL {$url} failed with status {$response->status()}"
            );
        }
    }
}
