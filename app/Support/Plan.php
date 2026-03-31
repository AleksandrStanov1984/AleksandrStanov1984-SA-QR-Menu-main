<?php


namespace App\Support;

use App\Models\Restaurant;
use App\Models\User;

class Plan
{
    public static function canUseBanners(User $user, Restaurant $restaurant): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return $restaurant->plan_key === 'pro';
    }
}
