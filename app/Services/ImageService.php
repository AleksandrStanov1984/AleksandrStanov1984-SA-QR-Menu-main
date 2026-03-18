<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ImageService
{
    public function url(?string $path): string
    {
        $fallback = config('image.urls.fallback', '/assets/system/fallback/food.webp');

        // если вообще нет пути
        if (!$path) {
            return $fallback;
        }

        $path = ltrim($path, '/');

        $fullPublicPath = public_path('assets/' . $path);

        // если файла нет — fallback
        if (!File::exists($fullPublicPath)) {
            return $fallback;
        }

        return '/assets/' . $path;
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
