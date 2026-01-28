<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $currentRestaurant = $request->attributes->get('admin_restaurant');

        $restaurants = [];
        if ($user?->is_super_admin) {
            $restaurants = Restaurant::query()->orderBy('name')->get();
        }

        return view('admin.dashboard.index', [
            'user' => $user,
            'currentRestaurant' => $currentRestaurant,
            'restaurants' => $restaurants,
        ]);
    }

    public function selectRestaurant(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        $data = $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
        ]);

        $request->session()->put('admin.restaurant_id', (int) $data['restaurant_id']);

        return redirect()->route('admin.restaurants.edit', $data['restaurant_id']);
    }
}
