<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\File;

class ImageService
{
    public function url(?string $path, ?string $title = null): string
    {
        $fallback = config('image.urls.fallback', '/assets/system/fallback/food.webp');

        // 1. если нет пути — решаем: это иконка или нет
        if (!$path) {

            // если есть title → считаем что это social icon
            if ($title) {
                return $this->socialIcon(null, $title);
            }

            return $fallback;
        }

        $path = ltrim($path, '/');

        // 2. прямой путь
        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        // 3. assets/...
        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        // 4. если путь есть, но файл не найден → fallback
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

        // базовая нормализация
        $key = str_replace(['.com', 'www.', 'https://', 'http://'], '', $key);
        $key = explode('/', $key)[0];
        $key = explode('.', $key)[0];

        // поиск по вхождению (ключевая часть)
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

        // 1. прямой путь
        if (File::exists(public_path($path))) {
            return '/' . $path;
        }

        // 2. assets/...
        $assetPath = 'assets/' . $path;

        if (File::exists(public_path($assetPath))) {
            return '/' . $assetPath;
        }

        // 3. fallback
        return '/assets/' . ltrim($fallback, '/');
    }

    public function og(string $locale): string
    {
        $locale = strtolower($locale);

        // 1. restaurant (будет потом)
        // $restaurantPath = "restaurants/{$id}/og/{$locale}/default.webp";

        // 2. system locale
        $localized = "system/og/{$locale}/default.webp";

        // 3. system default
        $global = "system/og/default.webp";

        if (File::exists(public_path("assets/{$localized}"))) {
            return "/assets/{$localized}";
        }

        if (File::exists(public_path("assets/{$global}"))) {
            return "/assets/{$global}";
        }

        return config('image.urls.fallback');
    }

    public function ogForLocale(Restaurant $restaurant, string $locale): string
    {
        $og = $meta['og'] ?? [];

        if (!empty($og[$locale])) {
            return '/assets/' . ltrim($og[$locale], '/');
        }

        $default = "assets/system/og/{$locale}/default.webp";

        if ($this->exists($default)) {
            return '/' . $default;
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
}
