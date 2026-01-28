<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantToken;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    public function qr(string $token)
    {
        $rt = RestaurantToken::where('token', $token)->firstOrFail();
        $restaurant = $rt->restaurant;

        abort_unless($restaurant->is_active, 404);

        return redirect()->route('restaurant.show', ['restaurant' => $restaurant->slug]);
    }

    public function show(Request $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        $locale = $request->query('lang', $restaurant->default_locale);
        $enabled = $restaurant->enabled_locales ?: [$restaurant->default_locale];
        if (!in_array($locale, $enabled, true)) {
            $locale = $restaurant->default_locale;
        }

        $sections = $restaurant->sections()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with([
                'translations' => fn($q) => $q->where('locale', $locale),
                'items' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'items.translations' => fn($q) => $q->where('locale', $locale),
            ])
            ->get();

        $template = $restaurant->template_key ?: 'classic';

        // --- overrides paths (storage) ---
        $overridesDir = "restaurants/{$restaurant->id}/overrides";
        $themeCssRel  = "{$overridesDir}/theme.css";
        $customCssRel = "{$overridesDir}/custom.css";
        $customJsRel  = "{$overridesDir}/custom.js";

        $theme_css_url  = file_exists(public_path("storage/{$themeCssRel}"))  ? asset("storage/{$themeCssRel}")  : null;
        $custom_css_url = file_exists(public_path("storage/{$customCssRel}")) ? asset("storage/{$customCssRel}") : null;
        $custom_js_url  = file_exists(public_path("storage/{$customJsRel}"))  ? asset("storage/{$customJsRel}")  : null;

        /**
         * Assets (images) base paths:
         *  - Template defaults (Vite): resources/resources/assets/{template}-menu/images
         *  - Restaurant overrides (storage): storage/app/public/restaurants/{id}/{template}-menu/images
         */
        $tplAssetBase = "resources/resources/assets/{$template}-menu/images"; // for Vite::asset()
        $restAssetBase = "storage/restaurants/{$restaurant->id}/{$template}-menu/images"; // for asset()

        return view("public.templates.$template", compact(
            'restaurant', 'sections', 'locale', 'enabled', 'template',
            'theme_css_url', 'custom_css_url', 'custom_js_url',
            'tplAssetBase', 'restAssetBase'
        ));
    }


}
