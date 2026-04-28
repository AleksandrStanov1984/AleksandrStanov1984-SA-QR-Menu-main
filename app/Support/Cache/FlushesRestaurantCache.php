<?php

namespace App\Support\Cache;

trait FlushesRestaurantCache
{
    protected function flushRestaurantCache($restaurant): void
    {
        if (!$restaurant) return;

        CacheHelper::flushRestaurant($restaurant->id);
    }
}
