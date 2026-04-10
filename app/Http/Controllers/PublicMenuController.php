<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantToken;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Support\PublicMenu\TemplateResolver;
use App\ViewModels\PublicMenu\MenuViewModel;

class PublicMenuController extends Controller
{
    public function show(Request $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        $restaurant->load([

            // =========================
            // SECTIONS
            // =========================
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

            // =========================
            // SOCIAL
            // =========================
            'socialLinks' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order');
            },

            // =========================
            // HOURS
            // =========================
            'hours',

            // =========================
            // BANNERS
            // =========================
            'banners' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit(5);
            },
        ]);

        $locale = $this->resolveLocale($request, $restaurant);

        app()->setLocale($locale);

        $vm = new MenuViewModel($restaurant, $locale);

        $view = app(TemplateResolver::class)->resolve($restaurant);

        return view($view, compact('vm'));
    }

    private function resolveLocale(Request $request, Restaurant $restaurant): string
    {
        $requested = $request->query('lang');

        $default = $restaurant->default_locale ?? 'de';

        if (!$requested) {
            return $default;
        }

        return in_array($requested, ['de', 'en', 'ru'], true)
            ? $requested
            : $default;
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
