<?php

namespace App\Support\Cache;

use Illuminate\Support\Facades\Cache;
use App\Models\Restaurant;

class CacheHelper
{
    public static function flushRestaurant(int $restaurantId): void
    {
        $store = config('cache.default');

        if (in_array($store, ['redis', 'memcached'])) {
            Cache::tags(["restaurant:{$restaurantId}"])->flush();
            return;
        }

        self::flushByLocales($restaurantId);
    }

    protected static function flushByLocales(int $restaurantId): void
    {
        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            return;
        }

        $locales = $restaurant->enabled_locales ?? [];

        if (empty($locales)) {
            $locales = [$restaurant->default_locale ?? 'de'];
        }

        foreach ($locales as $locale) {
            Cache::forget("menu:{$restaurantId}:{$locale}");
        }
    }
}
