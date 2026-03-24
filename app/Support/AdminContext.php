<?php

// app/Support/AdminContext.php
namespace App\Support;

use App\Models\Restaurant;

class AdminContext
{
    public static function restaurant(): ?Restaurant
    {
        $user = auth()->user();

        // ГЛАВНАЯ ЗАЩИТА
        if (!$user) {
            return null;
        }

        // 👤 обычный пользователь
        if (!$user->is_super_admin) {
            return $user->restaurant;
        }

        // сначала route
        $routeRestaurant = request()->route('restaurant');

        if ($routeRestaurant instanceof Restaurant) {
            return $routeRestaurant;
        }

        // потом session
        $id = session('restaurant_id');

        return $id ? Restaurant::find($id) : null;
    }
}
