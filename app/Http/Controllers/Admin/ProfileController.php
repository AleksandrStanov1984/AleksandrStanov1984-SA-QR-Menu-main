<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuPlan;
use App\Models\MenuTemplate;
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

        if ($user->is_super_admin) {

            $restaurant = $request->route('restaurant')
                ?? \App\Support\AdminContext::actingRestaurant();

        } else {
            $restaurant = $user->restaurant;
        }

        // ===== PERMISSIONS =====
        $grouped = Permissions::groupedRegistry();
        ksort($grouped);

        foreach ($grouped as &$items) {
            sort($items);
        }
        unset($items);

        $flat = array_merge(...array_values($grouped));

        // ===== DATA =====
        $templates = \App\Models\MenuTemplate::all();
        $plans = \App\Models\MenuPlan::all();

        return view('admin.profile', [
            'user' => $user,
            'restaurant' => $restaurant,
            'permissions_grouped' => $grouped,
            'permissions' => $flat,
            'templates' => $templates,
            'plans' => $plans,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $data['name'],
        ]);

        return back()->with('status', __('admin.profile.saved'));
    }

    public function updateRestaurant(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();

        if ($resp = Permissions::denyRedirect(auth()->user(), 'restaurant.profile.edit')) {
            return $resp;
        }

        $isSuper = (bool) ($user?->is_super_admin ?? false);

        $data = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],

            'phone' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'street' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:50'],

            'template_key' => [$isSuper ? 'required' : 'nullable', 'exists:menu_templates,key'],
            'plan_key' => [$isSuper ? 'required' : 'nullable', 'exists:menu_plans,key'],
        ]);

        $updateData = [
            'name' => $data['restaurant_name'],
            'contact_name' => $data['contact_name'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'street' => $data['street'] ?? null,
            'house_number' => $data['house_number'] ?? null,
        ];

        if ($isSuper) {
            $updateData['template_key'] = $data['template_key'];
            $updateData['plan_key'] = $data['plan_key'];
        }

        if ($isSuper && isset($data['plan_key'])) {
            // тут позже можно:
            // - лог
            // - событие
            // - пересчёт social links
        }

        $restaurant->update($updateData);

        return back()->with('status', __('admin.profile.restaurant.saved'));
    }
}
