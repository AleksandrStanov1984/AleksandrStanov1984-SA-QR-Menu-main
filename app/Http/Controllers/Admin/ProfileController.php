<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Support\Permissions;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        /** @var Restaurant|null $restaurant */
        $restaurant = $request->attributes->get('admin_restaurant');

        $grouped = \App\Support\Permissions::groupedRegistry();

        ksort($grouped);
        foreach ($grouped as $g => $items) {
            sort($items);
            $grouped[$g] = $items;
        }

        $flat = [];
        foreach ($grouped as $items) {
            foreach ($items as $label) $flat[] = $label;
        }

        return view('admin.profile', [
            'user' => $user,
            'restaurant' => $restaurant,
            'permissions_grouped' => $grouped,
            'permissions' => $flat,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->name = $data['name'];
        $user->save();

        return back()->with('status', __('admin.profile.saved'));
    }

    public function updateRestaurant(Request $request): RedirectResponse
    {
        /** @var Restaurant|null $restaurant */
        $restaurant = $request->attributes->get('admin_restaurant');
        abort_unless($restaurant, 404);

        Permissions::abortUnless($request->user(), 'restaurant.profile.edit');

        $data = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],

            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'street' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:50'],
        ]);

        $restaurant->name = $data['restaurant_name'];
        $restaurant->contact_name = $data['contact_name'] ?? null;
        $restaurant->contact_email = $data['contact_email'] ?? null;
        $restaurant->phone = $data['phone'] ?? null;

        $restaurant->city = $data['city'] ?? null;
        $restaurant->postal_code = $data['postal_code'] ?? null;
        $restaurant->street = $data['street'] ?? null;
        $restaurant->house_number = $data['house_number'] ?? null;

        $restaurant->save();

        return back()->with('status', __('admin.profile.restaurant.saved'));
    }
}
