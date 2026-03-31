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

        //  обычный пользователь
        if (!$user->is_super_admin) {
            return $user->restaurant;
        }

        //  route приоритет
        $routeRestaurant = request()->route('restaurant');

        if ($routeRestaurant instanceof Restaurant) {
            return $routeRestaurant;
        }

        //  session
        $id = session('acting_restaurant_id');

        if (!$id) {
            return null;
        }

        return Restaurant::where('id', $id)
            ->where('is_active', true)
            ->first();
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
