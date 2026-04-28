<?php

namespace App\Http\Middleware\Cache;

use Closure;
use Illuminate\Http\Request;
use App\Support\Cache\CacheHelper;
use App\Support\AdminContext;

class FlushRestaurantCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        if (!$request->is('admin/*')) {
            return $response;
        }

        $restaurant = AdminContext::actingRestaurant();

        if ($restaurant) {
            CacheHelper::flushRestaurant($restaurant->id);
        }

        return $response;
    }
}
