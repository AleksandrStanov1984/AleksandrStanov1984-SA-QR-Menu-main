<?php

namespace Tests\Performance\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->actingAs($this->user);
    }

    public function test_admin_dashboard_loads_fast(): void
    {
        $start = microtime(true);

        $response = $this->get('/admin');

        $duration = microtime(true) - $start;

        $response->assertStatus(200);

        $this->assertLessThan(
            0.6,
            $duration,
            "Admin dashboard too slow: {$duration}s"
        );
    }

    public function test_login_is_fast(): void
    {
        auth()->logout(); // важно, иначе уже залогинен

        $password = 'password';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $start = microtime(true);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $duration = microtime(true) - $start;

        $this->assertTrue(
            in_array($response->status(), [200, 302]),
            'Login failed'
        );

        $this->assertLessThan(
            0.6,
            $duration,
            "Login too slow: {$duration}s"
        );
    }

    public function test_admin_routes_are_fast(): void
    {
        $restaurant = Restaurant::factory()->create();

        $routes = [
            "/admin",
            "/admin/restaurants",
            "/admin/restaurants/{$restaurant->id}/menu",
            "/admin/restaurants/{$restaurant->id}/profile",
            "/admin/restaurants/{$restaurant->id}/branding",
            "/admin/restaurants/{$restaurant->id}/hours",
            "/admin/restaurants/{$restaurant->id}/qr",
        ];

        foreach ($routes as $route) {
            $start = microtime(true);

            $response = $this->get($route);

            $duration = microtime(true) - $start;

            $response->assertStatus(200);

            $this->assertLessThan(
                0.6,
                $duration,
                "Route {$route} too slow: {$duration}s"
            );
        }
    }

    public function test_item_save_is_fast(): void
    {
        $restaurant = Restaurant::factory()->create();

        $section = Section::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_active' => true,
        ]);

        $start = microtime(true);

        $response = $this->post(
            route('admin.restaurants.items.store', [$restaurant, $section]),
            [
                'price' => 12.50,
                'currency' => 'EUR',

                'title' => [
                    'de' => 'Perf Item',
                ],
                'description' => [
                    'de' => 'Perf description',
                ],

                'is_active' => 1,
            ]
        );

        $duration = microtime(true) - $start;

        $this->assertTrue(
            in_array($response->status(), [200, 302], true),
            'Item save failed'
        );

        $this->assertLessThan(
            0.6,
            $duration,
            "Item save too slow: {$duration}s"
        );
    }
}
