<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantToken;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Support\PublicMenu\TemplateResolver;
use App\ViewModels\PublicMenu\MenuViewModel;

class PublicMenuController extends Controller
{
    public function show(Request $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        $restaurant->load([

            'sections' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order');
            },

            'sections.translations',

            'sections.items' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order');
            },

            'sections.items.translations',

            'socialLinks' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order');
            },

            'hours',

            'banners' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit(5);
            },
        ]);

        // LOCALE
        $locale = $this->resolveLocale($request, $restaurant);

        // CACHE KEY
        $cacheKey = "menu:{$restaurant->id}:{$locale}";

        // CACHE VIEWMODEL
        $vm = Cache::remember($cacheKey, 300, function () use ($restaurant, $locale) {
            return new MenuViewModel($restaurant, $locale);
        });

        $view = app(TemplateResolver::class)->resolve($restaurant);

        // HTTP CACHE (30 сек)
        return response()
            ->view($view, compact('vm'))
            ->header('Cache-Control', 'public, max-age=30');
    }

    private function resolveLocale(Request $request, Restaurant $restaurant): string
    {
        $enabled = $restaurant->enabled_locales ?: ['de'];
        $default = $restaurant->default_locale ?: 'de';

        if (!in_array($default, $enabled, true)) {
            $default = $enabled[0] ?? 'de';
        }

        $locale = $request->query('lang')
            ?? session('public_locale')
            ?? $request->cookie('menu_locale')
            ?? $default;

        if (!in_array($locale, $enabled, true)) {
            $locale = $default;

            session()->forget('public_locale');
            cookie()->queue(cookie()->forget('menu_locale'));
        }

        app()->setLocale($locale);

        session(['public_locale' => $locale]);
        cookie()->queue('menu_locale', $locale, 60 * 24 * 30);

        return $locale;
    }

    public function qr(string $token): RedirectResponse
    {
        $record = RestaurantToken::where('token', $token)->firstOrFail();

        $restaurant = $record->restaurant;

        abort_unless($restaurant && $restaurant->is_active, 404);

        return redirect()->to(
            route('restaurant.show', $restaurant->slug, false)
        );
    }
}
