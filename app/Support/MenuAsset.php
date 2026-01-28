<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;

class MenuAsset
{
    /**
     * Resolve image URL with fallback:
     *
     * Priority:
     * 1) Restaurant override in public/storage/...
     * 2) Template asset via Vite (resources/resources/assets/...)
     * 3) Global fallback (public/storage/common/image-fallback.png)
     *
     * @param string $restAssetBase e.g. "storage/restaurants/1/classic-menu/images"
     * @param string $tplAssetBase  e.g. "resources/resources/assets/classic-menu/images"
     * @param string $relativePath e.g. "bg_day.png" or "category-menu/meat/meat.png"
     */
    public static function image(
        string $restAssetBase,
        string $tplAssetBase,
        string $relativePath
    ): string {
        $relativePath = ltrim($relativePath, '/');

        /*
        |--------------------------------------------------------------------------
        | 1) Restaurant override (public/storage/...)
        |--------------------------------------------------------------------------
        */
        $restRel = rtrim($restAssetBase, '/') . '/' . $relativePath;

        if (file_exists(public_path($restRel))) {
            return asset($restRel);
        }

        /*
        |--------------------------------------------------------------------------
        | 2) Template asset via Vite
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | On Windows Vite often does NOT see "resources/resources/assets"
        | so Vite::asset() may throw exception.
        | We MUST protect against that.
        */
        $tplRel = rtrim($tplAssetBase, '/') . '/' . $relativePath;

        try {
            return Vite::asset($tplRel);
        } catch (\Throwable $e) {
            // fall through to global fallback
        }

        /*
        |--------------------------------------------------------------------------
        | 3) Global fallback (guaranteed not to crash)
        |--------------------------------------------------------------------------
        */
        $fallback = 'storage/common/image-fallback.png';

        if (file_exists(public_path($fallback))) {
            return asset($fallback);
        }

        /*
        |--------------------------------------------------------------------------
        | 4) Absolute last fallback — empty SVG (never 500)
        |--------------------------------------------------------------------------
        */
        return 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"/>';
    }
}
