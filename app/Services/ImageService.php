<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\File;

class ImageService
{
    public function url(?string $path, ?string $title = null): string
    {
        $fallback = config('image.urls.fallback', '/assets/system/fallback/food.webp');

        if (!$path) {

            if ($title) {
                return $this->socialIcon(null, $title);
            }

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

        if ($title) {
            return $this->socialIcon(null, $title);
        }

        return $fallback;
    }

    public function delete(string $path): void
    {
        if (!$path) return;

        $path = ltrim($path, '/');

        $full = public_path('assets/' . $path);

        // SVG — удаляем напрямую
        if (str_ends_with($full, '.svg')) {
            if (File::exists($full)) {
                File::delete($full);
            }
            return;
        }

        //  WEBP + RETINA
        $webp = $full;
        $retina = str_replace('.webp', '@2x.webp', $full);

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

    public function socialIcon(?string $path, ?string $title = null): string
    {
        if ($path) {
            return $this->url($path);
        }

        $map = config('social_icons.map', []);
        $fallback = config('social_icons.fallback', 'website.svg');
        $base = config('social_icons.base_path', 'assets/system/icons');

        $key = strtolower(trim($title ?? ''));

        $key = str_replace(['.com', 'www.', 'https://', 'http://'], '', $key);
        $key = explode('/', $key)[0];
        $key = explode('.', $key)[0];

        foreach ($map as $social => $file) {
            if (str_contains($key, $social)) {
                return asset($base . '/' . $file);
            }
        }

        return asset($base . '/' . $fallback);
    }

    public function banner(?string $path): string
    {
        $fallback = config('image.system.fallbacks.banner', 'system/banners/default.webp');

        if (!$path) {
            return '/assets/' . ltrim($fallback, '/');
        }

        $path = ltrim($path, '/');

        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        return '/assets/' . ltrim($fallback, '/');
    }

    public function ogForLocale(Restaurant $restaurant, string $locale): string
    {
        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];
        $og = $meta['og'] ?? [];

        if (!empty($og[$locale])) {

            $path = 'assets/' . ltrim($og[$locale], '/');

            if (File::exists(public_path($path))) {
                return '/' . $path;
            }
        }

        $localized = "assets/system/og/{$locale}/default.webp";
        if (File::exists(public_path($localized))) {
            return '/' . $localized;
        }

        return '/assets/system/og/default.webp';
    }

    private function normalizeAssetPath(string $path): string
    {
        $path = ltrim($path, '/');

        // если уже начинается с assets → не дублируем
        if (str_starts_with($path, 'assets/')) {
            return '/' . $path;
        }

        return '/assets/' . $path;
    }

    private function exists(string $path): bool
    {
        return is_file(public_path(ltrim($path, '/')));
    }

    public function path(string $path): string
    {
        return public_path('assets/' . ltrim($path, '/'));
    }

    public function existsPublic(string $path): bool
    {
        return is_file($this->path($path));
    }

    public function food(?string $path): string
    {
        $fallback = config('image.system.fallbacks.food', 'system/fallback/food.webp');

        if (!$path) {
            return '/assets/' . $fallback;
        }

        $path = ltrim($path, '/');

        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        return '/assets/' . $fallback;
    }

    public function logo(?string $path): string
    {
        $fallback = config('image.system.fallbacks.logo', 'system/logo/logo.svg');

        if (!$path) {
            return '/assets/' . ltrim($fallback, '/');
        }

        $path = ltrim($path, '/');

        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        return '/assets/' . ltrim($fallback, '/');
    }
}
