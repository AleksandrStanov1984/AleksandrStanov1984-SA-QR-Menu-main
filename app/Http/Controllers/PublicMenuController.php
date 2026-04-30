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
        $vm = $this->buildVm($request, $restaurant);

        $view = app(TemplateResolver::class)->resolve($restaurant);

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

    protected function buildVm(Request $request, Restaurant $restaurant): MenuViewModel
    {
        abort_unless($restaurant->is_active, 404);

        $restaurant->load([

            'sections' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sections.translations',

            'sections.items' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'sections.items.translations',

            'socialLinks' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),

            'hours',

            'banners' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')->limit(5),
        ]);

        $locale = $this->resolveLocale($request, $restaurant);

        $cacheKey = "menu:{$restaurant->id}:{$locale}";

        return Cache::remember($cacheKey, 300, function () use ($restaurant, $locale) {
            return new MenuViewModel($restaurant, $locale);
        });
    }

    public function impressum(Request $request, Restaurant $restaurant)
    {
        $vm = $this->buildVm($request, $restaurant);

        $legal = $this->buildLegal('impressum', $restaurant);

        return response()
            ->view('legal.impressum', compact('vm', 'legal'))
            ->header('Cache-Control', 'public, max-age=30');
    }

    public function datenschutz(Request $request, Restaurant $restaurant)
    {
        $vm = $this->buildVm($request, $restaurant);

        $legal = $this->buildLegal('datenschutz', $restaurant);

        return response()
            ->view('legal.datenschutz', compact('vm', 'legal'))
            ->header('Cache-Control', 'public, max-age=30');
    }

    private function buildLegal(string $type, Restaurant $restaurant): array
    {
        $data = trans("legal.{$type}");

        $search = [
            ':owner_name',
            ':owner_address_line_1',
            ':owner_address_line_2',
            ':owner_country',
            ':owner_email',
            ':owner_phone',
        ];

        $replace = [
            e($restaurant->contact_name),
            e($restaurant->street . ' ' . $restaurant->house_number),
            e($restaurant->postal_code . ' ' . $restaurant->city),
            'Germany',
            e($restaurant->contact_email),
            e($restaurant->phone),
        ];

        array_walk_recursive($data, function (&$value) use ($search, $replace) {
            $value = str_replace($search, $replace, $value);

            // mailto fix
            $value = str_replace(
                'mailto::owner_email',
                'mailto:' . e($replace[4]),
                $value
            );

            // tel fix
            $value = str_replace(
                'tel::owner_phone',
                'tel:' . preg_replace('/\s+/', '', $replace[5]),
                $value
            );
        });

        return $data;
    }
}
