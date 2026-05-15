<?php

namespace App\Services\Restaurants;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestaurantDeletionService
{
    public function delete(Restaurant $restaurant): void
    {
        DB::transaction(function () use ($restaurant) {

            // =========================
            // LOAD RELATIONS
            // =========================
            $restaurant->load([
                'sections.items.translations',
                'sections.translations',

                'socialLinks',
                'hours',
                'banners',

                'billingRecords',

                'token',
                'qr',
            ]);

            // =========================
            // DELETE ITEMS
            // =========================
            foreach ($restaurant->sections as $section) {

                foreach ($section->items as $item) {

                    $item->translations()->delete();

                    $item->delete();
                }

                $section->translations()->delete();

                $section->delete();
            }

            // =========================
            // DIRECT RELATIONS
            // =========================
            $restaurant->socialLinks()->delete();

            $restaurant->hours()->delete();

            $restaurant->banners()->delete();

            $restaurant->billingRecords()->delete();

            $restaurant->token()?->delete();

            $restaurant->qr()?->delete();

            // =========================
            // DELETE RESTAURANT
            // =========================
            $restaurant->delete();
        });

        // =========================
        // FILESYSTEM CLEANUP
        // =========================
        $id = $restaurant->id;

        File::deleteDirectory(
            storage_path("app/restaurants/{$id}")
        );

        File::delete(
            storage_path("app/tmp/import-mapping/{$id}.json")
        );

        // =========================
        // CACHE CLEANUP
        // =========================
        Cache::forget("import:status:{$id}");

        Cache::forget("import:result:{$id}");
    }
}
