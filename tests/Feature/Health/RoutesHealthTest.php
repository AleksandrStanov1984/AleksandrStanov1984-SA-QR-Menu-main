<?php

namespace Tests\Feature\Health;

use Tests\TestCase;
use App\Models\User;
use App\Models\Section;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesHealthTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create([
            'slug' => 'health-test',
        ]);

        Section::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $this->admin = User::factory()->create([
            'is_super_admin' => true,
        ]);
    }

    public function test_main_routes_return_200()
    {
        $urls = [
            '/',
            '/admin/login',
            '/r/' . $this->restaurant->slug,
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);

            $this->assertTrue(
                in_array($response->status(), [200, 302]),
                "URL {$url} failed with status {$response->status()}"
            );
        }
    }

    public function test_public_routes_return_200(): void
    {
        $routes = [
            "/r/{$this->restaurant->slug}",
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertStatus(200);
        }
    }

    public function test_admin_routes_return_200(): void
    {
        $this->actingAs($this->admin);

        $routes = [
            "/admin",
            "/admin/restaurants",
            "/admin/restaurants/{$this->restaurant->id}/menu",
            "/admin/restaurants/{$this->restaurant->id}/profile",
            "/admin/restaurants/{$this->restaurant->id}/branding",
            "/admin/restaurants/{$this->restaurant->id}/hours",
            "/admin/restaurants/{$this->restaurant->id}/qr",
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertStatus(200);
        }
    }

    public function test_all_get_routes_return_valid_status()
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {

            if (!in_array('GET', $route->methods())) {
                continue;
            }

            $uri = $route->uri();

            // пропускаем динамику
            if (str_contains($uri, '{')) {
                continue;
            }

            $response = $this->get('/' . $uri);

            $this->assertTrue(
                in_array($response->status(), [200, 302]),
                "Route /{$uri} failed with {$response->status()}"
            );
        }
    }
}
