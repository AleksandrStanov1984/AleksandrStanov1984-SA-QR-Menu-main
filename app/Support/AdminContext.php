<?php


namespace App\Support;

use App\Models\Restaurant;

class AdminContext
{
    public static function userRestaurant(): ?Restaurant
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return $user->restaurant;
    }

    public static function actingRestaurant(): ?Restaurant
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // =========================
        // ROUTE
        // =========================
        $routeRestaurant = request()->route('restaurant');

        if ($routeRestaurant instanceof Restaurant) {
            return $routeRestaurant;
        }

        // =========================
        // SESSION
        // =========================
        $id = session('acting_restaurant_id');

        if ($id) {
            return Restaurant::where('id', $id)
                ->where('is_active', true)
                ->first();
        }

        // =========================
        // FALLBACK
        // =========================
        if (!$user->is_super_admin && $user->restaurant) {
            self::setActingRestaurant($user->restaurant);
            return $user->restaurant;
        }

        return null;
    }

    public static function setActingRestaurant(Restaurant $restaurant): void
    {
        session(['acting_restaurant_id' => $restaurant->id]);
    }

    public static function clearActingRestaurant(): void
    {
        session()->forget('acting_restaurant_id');
    }
}
