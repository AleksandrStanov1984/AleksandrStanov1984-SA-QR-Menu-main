<?php

namespace App\Observers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Storage;

class RestaurantObserver
{
    public function created(Restaurant $restaurant): void
    {
        // Init per-restaurant storage folders for uploaded assets.
        $base = "restaurants/{$restaurant->id}";
        $dirs = [
            "$base/items",
            "$base/banners",
            "$base/backgrounds",
            "$base/socials",
            "$base/imports",
            "$base/misc",
        ];

        foreach ($dirs as $dir) {
            Storage::disk('public')->makeDirectory($dir);
        }
    }
}
