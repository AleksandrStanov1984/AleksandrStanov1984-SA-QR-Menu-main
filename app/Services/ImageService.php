<?php

namespace App\Services;

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

        $map = [
            'facebook'  => 'facebook.svg',
            'instagram' => 'instagram.svg',
            'whatsapp'  => 'whatsapp.svg',
            'telegram'  => 'telegram.svg',
            'tiktok'    => 'tiktok.svg',
        ];

        $key = strtolower(trim($title ?? ''));

        //  нормализация
        $key = str_replace(['.com', 'www.', 'https://', 'http://'], '', $key);
        $key = explode('/', $key)[0];

        return '/assets/system/icons/' . ($map[$key] ?? 'link.svg');
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
}
