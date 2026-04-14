<?php

use App\Models\Restaurant;
use Illuminate\Routing\Route;

if (!function_exists('ctxRestaurant')) {
    function ctxRestaurant(): ?Restaurant
    {
        return \App\Support\AdminContext::actingRestaurant();
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

if (!function_exists('profile_route')) {
    function profile_route(?Restaurant $restaurant = null): string
    {
        return $restaurant
            ? route('admin.profile', ['restaurant' => $restaurant->id])
            : route('admin.profile.global');
    }
}
