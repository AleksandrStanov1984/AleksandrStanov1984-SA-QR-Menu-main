<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ImageService
{
    public function url(?string $path): string
    {
        $fallback = config('image.urls.fallback', '/assets/system/fallback/food.webp');

        if (!$path) {
            return $fallback;
        }

        $path = ltrim($path, '/');

        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        $iconPath = 'assets/system/icons/' . $path;

        if (File::exists(public_path($iconPath))) {
            return '/' . $iconPath;
        }

        return $fallback;
    }

    public function delete(string $path): void
    {
        $base = public_path('assets/' . $path);

        $webp = $base;
        $retina = str_replace('.webp', '@2x.webp', $base);

        if (File::exists($webp)) {
            File::delete($webp);
        }

        if (File::exists($retina)) {
            File::delete($retina);
        }
    }

    public function restaurantQrPath(int $restaurantId, string $file): string
    {
        return "restaurants/{$restaurantId}/qr/{$file}";
    }

    public function qr(?string $path): string
    {

        $fallback = config('image.system.qr', '/assets/system/qr/fallback.webp');

        if (!$path) {
            return $fallback;
        }

        $path = ltrim($path, '/');

        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        $iconPath = 'assets/system/icons/' . $path;

        if (File::exists(public_path($iconPath))) {
            return '/' . $iconPath;
        }

        return $fallback;
    }
}
