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

        // ✅ 1. сначала пробуем как есть (на случай assets/... или полного пути)
        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        // ✅ 2. основной кейс: добавляем assets/
        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        // ✅ 3. system icons (uuid.svg)
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
}
