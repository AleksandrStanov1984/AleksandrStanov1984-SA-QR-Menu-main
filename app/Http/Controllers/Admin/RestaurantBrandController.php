<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantBrandController extends Controller
{
    public function update(Request $request, Restaurant $restaurant)
    {
        // защита: если не super admin — можно обновлять только свой ресторан
        $user = $request->user();
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        // удалить старый файл
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
        $user = $request->user();

        // 1) обычный пользователь может менять ТОЛЬКО свой ресторан
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        // 2) проверка права branding_manage (для super admin всегда ок)
        $canBranding = $user->is_super_admin || (($user->permissions['branding_manage'] ?? false) === true);
        abort_unless($canBranding, 403);

        // 3) валидация файлов (mime + max)
        $data = $request->validate([
            'bg_light' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'bg_dark'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        // meta может быть null/не массив
        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];

        // удалить старые файлы если грузим новые
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

        $restaurant->meta = $meta;
        $restaurant->save();

        return back()->with('status', 'Фон обновлён');
    }
}
