<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuProfileController extends Controller
{
    public function edit(Request $request)
    {
        $restaurantId = $request->session()->get('admin.restaurant_id');

        // если обычный пользователь привязан к ресторану
        if (!$restaurantId && $request->user()) {
            $restaurantId = $request->user()->restaurant_id ?? null;
        }

        // если супер-админ не выбрал ресторан — отправим на список ресторанов
        if (!$restaurantId) {
            return redirect()->route('admin.restaurants.index');
        }

        return redirect()->route('admin.restaurants.edit', ['restaurant' => $restaurantId]);
    }
}
