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
}
