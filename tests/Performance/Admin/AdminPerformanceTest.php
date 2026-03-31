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

        fwrite(STDOUT, "test_admin_dashboard_loads_fast() -> Time: {$duration}s\n");
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

        fwrite(STDOUT, "test_login_is_fast() -> Time: {$duration}s\n");
    }

    public function test_all_routes_are_fast(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $restaurant = Restaurant::factory()->create();

        User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_super_admin' => false,
        ]);

        $this->actingAs($admin);

        $routes = [
            "/admin",
            "/admin/restaurants",
            "/admin/restaurants/create",

            "/admin/restaurants/{$restaurant->id}/edit",
            "/admin/restaurants/{$restaurant->id}/menu",
            "/admin/restaurants/{$restaurant->id}/profile",
            "/admin/restaurants/{$restaurant->id}/branding",
            "/admin/restaurants/{$restaurant->id}/hours",
            "/admin/restaurants/{$restaurant->id}/qr",
            "/admin/restaurants/{$restaurant->id}/sections",
            "/admin/restaurants/{$restaurant->id}/socials",
            "/admin/restaurants/{$restaurant->id}/import",
            "/admin/restaurants/{$restaurant->id}/credentials",

            "/admin/profile",
            "/admin/security/password",

            "/r/{$restaurant->slug}",
        ];

        foreach ($routes as $route) {

            $start = microtime(true);

            $response = $this->get($route);

            $duration = microtime(true) - $start;

            if ($response->status() !== 200) {
                fwrite(STDOUT, "FAILED ROUTE: {$route}\n");
            }

            $response->assertStatus(200);

            $this->assertLessThan(
                0.6,
                $duration,
                "Route {$route} too slow: {$duration}s"
            );

            fwrite(STDOUT, "Route {$route} -> Time: {$duration}s\n");
        }
    }

    public function test_admin_menu_queries_count(): void
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => true,
        ]);

        $restaurant = \App\Models\Restaurant::factory()->create();

        $this->actingAs($user);

        \DB::enableQueryLog();

        $response = $this->get("/admin/restaurants/{$restaurant->id}/menu");

        $response->assertStatus(200);

        $queries = \DB::getQueryLog();
        $count = count($queries);

        fwrite(STDOUT, "Admin menu queries: {$count}\n");

        $this->assertLessThan(
            20,
            $count,
            "Too many queries on admin menu: {$count}"
        );
    }

    public function test_public_menu_queries_count(): void
    {
        $restaurant = \App\Models\Restaurant::factory()->create();

        \DB::enableQueryLog();

        $response = $this->get("/r/{$restaurant->slug}");

        $response->assertStatus(200);

        $queries = \DB::getQueryLog();
        $count = count($queries);

        fwrite(STDOUT, "Public menu queries: {$count}\n");

        $this->assertLessThan(
            15,
            $count,
            "Too many queries on public menu: {$count}"
        );
    }

    public function test_all_routes_are_fast_under_load(): void
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

            // PUBLIC
            "/r/{$restaurant->slug}",
        ];

        foreach ($routes as $route) {

            for ($i = 0; $i < 10; $i++) {

                $start = microtime(true);

                $response = $this->get($route);

                $duration = microtime(true) - $start;

                $response->assertStatus(200);

                $this->assertLessThan(
                    0.6,
                    $duration,
                    "Route {$route} too slow on iteration {$i}: {$duration}s"
                );

                fwrite(STDOUT, "Route {$route} [{$i}] -> {$duration}s\n");
            }
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

        fwrite(STDOUT, "test_item_save_is_fast() -> Time: {$duration}s\n");
    }
}
