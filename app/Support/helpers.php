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
