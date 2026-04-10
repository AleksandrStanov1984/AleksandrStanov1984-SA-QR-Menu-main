<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $restaurantId = $request->session()->get('admin.restaurant_id')
            ?? $user?->restaurant_id;

        if (!$restaurantId) {
            return redirect()->route('admin.restaurants.index');
        }

        return redirect()->route('admin.restaurants.edit', [
            'restaurant' => $restaurantId
        ]);
    }
}
