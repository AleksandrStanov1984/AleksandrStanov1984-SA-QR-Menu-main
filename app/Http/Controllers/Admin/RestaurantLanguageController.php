<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Models\Restaurant;
use App\Support\Guards\AccessGuardTrait;
use Illuminate\Http\Request;

class RestaurantLanguageController
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function index(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        return view('admin.restaurants.languages', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $allLocales = config('locales.all', ['de']);
        $limit = $restaurant->feature('locales_limit', 1);

        $default = $request->input('default_locale', $restaurant->default_locale ?? 'de');
        $enabled = $request->input('enabled_locales', []);

        $enabled = is_array($enabled) ? $enabled : [];

        $enabled = array_values(array_intersect($enabled, $allLocales));

        if (!in_array($default, $allLocales, true)) {
            $default = $restaurant->default_locale ?? 'de';

            if (!in_array($default, $allLocales, true)) {
                $default = $allLocales[0] ?? 'de';
            }
        }

        if (!in_array($default, $enabled, true)) {
            $enabled[] = $default;
        }

        $enabled = array_values(array_unique($enabled));

        if ($limit !== null) {

            if (count($enabled) > $limit) {
                $last = collect($enabled)
                    ->reverse()
                    ->first(fn ($locale) => $locale !== $default);

                $enabled = [$default];

                if ($last && $last !== $default) {
                    $enabled[] = $last;
                }
            }
        }

        if (empty($enabled)) {
            $enabled = [$default];
        }

        $restaurant->update([
            'default_locale' => $default,
            'enabled_locales' => $enabled,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'default_locale' => $default,
                'enabled_locales' => $enabled,
                'limit' => $limit,
                'message' => __('profile.languages.updated'),
            ]);
        }

        return back()->with('status', __('profile.languages.updated'));
    }
}
