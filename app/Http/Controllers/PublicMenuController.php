<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Support\PublicMenu\TemplateResolver;
use App\ViewModels\PublicMenu\MenuViewModel;

class PublicMenuController extends Controller
{
    public function show(Request $request, Restaurant $restaurant)
    {
        abort_unless($restaurant->is_active, 404);

        $restaurant->load([
            'sections.items.translations',
            'sections.translations',
            'socialLinks',
            'hours'
        ]);

        $locale = $this->resolveLocale($request, $restaurant);

        $vm = new MenuViewModel($restaurant, $locale);

        $view = app(TemplateResolver::class)->resolve($restaurant);

        return view($view, compact('vm'));
    }

    private function resolveLocale(Request $request, Restaurant $restaurant): string
    {
        $requested = $request->query('lang');

        $supported = method_exists($restaurant, 'languages')
            ? ($restaurant->languages?->pluck('code')->toArray() ?? ['de'])
            : ['de'];

        if ($requested && in_array($requested, $supported)) {
            return $requested;
        }

        return $restaurant->default_locale ?? 'de';
    }
}
