<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\Permissions;

class RestaurantBrandController extends Controller
{
    private function assertRestaurantScope(Request $request, Restaurant $restaurant): void
    {
        $user = $request->user();

        // user -> только свой ресторан
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantScope($request, $restaurant);

        $user = $request->user();

        // permission: upload logo
        Permissions::abortUnless($user, 'branding.logo.upload');

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3000'],
        ]);

        if (!empty($restaurant->logo_path)) {
            Storage::disk('public')->delete($restaurant->logo_path);
        }

        $path = $request->file('logo')->store(
            "restaurants/{$restaurant->id}/brand",
            'public'
        );

        $restaurant->update(['logo_path' => $path]);

        return back()->with('success', __('admin.restaurants.brand.logo_saved'));
    }

    public function updateBackgrounds(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantScope($request, $restaurant);

        $user = $request->user();

        // Разрешаем вызов метода, если есть ХОТЯ БЫ одно право:
        $canBg   = Permissions::can($user, 'branding.backgrounds.upload');
        $canMode = Permissions::can($user, 'branding.theme_mode.edit');

        abort_unless($canBg || $canMode, 403);

        // validate: theme_mode + images
        $data = $request->validate([
            'theme_mode' => ['nullable', 'in:auto,light,dark'],
            'bg_light'   => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'bg_dark'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];

        // дефолт при отсутствии
        $meta['theme_mode'] = $meta['theme_mode'] ?? 'light';

        // theme_mode сохраняем только при праве
        if ($canMode && $request->filled('theme_mode')) {
            $meta['theme_mode'] = $request->input('theme_mode'); // auto|light|dark
        }

        // backgrounds сохраняем только при праве
        if ($canBg) {
            if ($request->hasFile('bg_light')) {
                if (!empty($meta['bg_light'])) {
                    Storage::disk('public')->delete($meta['bg_light']);
                }

                $meta['bg_light'] = $request->file('bg_light')->store(
                    "restaurants/{$restaurant->id}/brand",
                    'public'
                );
            }

            if ($request->hasFile('bg_dark')) {
                if (!empty($meta['bg_dark'])) {
                    Storage::disk('public')->delete($meta['bg_dark']);
                }

                $meta['bg_dark'] = $request->file('bg_dark')->store(
                    "restaurants/{$restaurant->id}/brand",
                    'public'
                );
            }
        }

        $restaurant->meta = $meta;
        $restaurant->save();

        return back()->with('status', 'Фон обновлён');
    }
}
