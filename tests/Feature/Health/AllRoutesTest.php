<?php

namespace Tests\Feature\Health;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class AllRoutesTest extends TestCase
{
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
