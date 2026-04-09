<?php

namespace App\Support\ImagePipeline;

use Illuminate\Support\Facades\File;

class RestaurantAssetsService
{
    public function create(int $restaurantId): void
    {
        if ($restaurantId <= 0) return;

        // =============================
        // STORAGE (image-inbox)
        // =============================
        $storageBase = storage_path("app/image-inbox/assets/restaurants/{$restaurantId}");

        $storageDirs = [
            'banners',

            // BRANDING
            'branding/logo',
            'branding/backgrounds/light',
            'branding/backgrounds/dark',

            // MENU
            'menu/items',

            // QR
            'qr/raw',
            'qr/logo',
            'qr/background',
            'qr/final',
        ];

        foreach ($storageDirs as $dir) {
            try {
                File::ensureDirectoryExists("{$storageBase}/{$dir}", 0755, true);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // =============================
        // PUBLIC (runtime)
        // =============================
        $publicBase = public_path("assets/restaurants/{$restaurantId}");

        $publicDirs = [
            'banners',

            // BRANDING
            'branding/logo',
            'branding/backgrounds/light',
            'branding/backgrounds/dark',

            // MENU
            'menu/items',

            // QR
            'qr/raw',
            'qr/logo',
            'qr/background',
            'qr/final',
        ];

        foreach ($publicDirs as $dir) {
            try {
                File::ensureDirectoryExists("{$publicBase}/{$dir}", 0755, true);
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
