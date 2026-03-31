<?php

use App\Models\Restaurant;
use Illuminate\Routing\Route;

if (!function_exists('ctxRestaurant')) {
    function ctxRestaurant(): ?Restaurant
    {
        $route = request()->route();

        if (!$route instanceof Route) {
            return null;
        }

        $param = $route->parameter('restaurant');

        return $param instanceof Restaurant ? $param : null;
    }
}

if (!function_exists('appUrl')) {
    function appUrl(): string
    {
        if (app()->runningInConsole()) {
            return config('app.url');
        }

        $request = request();

        if ($request && $request->getSchemeAndHttpHost()) {
            return $request->getSchemeAndHttpHost();
        }

        return config('app.url');
    }
}
