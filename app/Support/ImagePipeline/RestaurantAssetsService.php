<?php

namespace App\Support\ImagePipeline;

use Illuminate\Support\Facades\File;

class RestaurantAssetsService
{
    public function create(int $restaurantId): void
    {
        if ($restaurantId <= 0) return;

        $directories = [
            storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/logo"),
            storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/hero"),
            storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/menu/items"),
            storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/categories"),
            storage_path("app/image-inbox/assets/restaurants/{$restaurantId}/gallery"),

            public_path("assets/restaurants/{$restaurantId}/logo"),
            public_path("assets/restaurants/{$restaurantId}/hero"),
            public_path("assets/restaurants/{$restaurantId}/menu/items"),
            public_path("assets/restaurants/{$restaurantId}/categories"),
            public_path("assets/restaurants/{$restaurantId}/gallery"),
        ];

        foreach ($directories as $dir) {
            try {
                File::ensureDirectoryExists($dir, 0755, true);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    public function delete(int $restaurantId): void
    {
        if ($restaurantId <= 0) return;

        $directories = [
            storage_path("app/image-inbox/assets/restaurants/{$restaurantId}"),
            public_path("assets/restaurants/{$restaurantId}"),
        ];

        foreach ($directories as $dir) {
            if (
                File::exists($dir) &&
                str_contains($dir, "restaurants/{$restaurantId}")
            ) {
                try {
                    File::deleteDirectory($dir);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }
}
