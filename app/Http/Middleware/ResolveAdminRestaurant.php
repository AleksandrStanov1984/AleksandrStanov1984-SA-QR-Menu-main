<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveAdminRestaurant
{
    /**
     * Resolves "current restaurant" for admin screens.
     * - Super admin can pick a restaurant; selection stored in session.
     * - Regular users are bound to `users.restaurant_id`.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('admin.login');
        }

        $currentRestaurantId = null;

        if ((bool) $user->is_super_admin) {
            $currentRestaurantId = (int) ($request->session()->get('admin.restaurant_id') ?? 0);
            if ($currentRestaurantId <= 0) {
                $currentRestaurantId = null;
            }
        } else {
            $currentRestaurantId = $user->restaurant_id ? (int) $user->restaurant_id : null;
        }

        $currentRestaurant = null;
        if ($currentRestaurantId) {
            $currentRestaurant = Restaurant::query()->find($currentRestaurantId);
        }

        // Share with request for controllers/views.
        $request->attributes->set('admin_restaurant', $currentRestaurant);

        return $next($request);
    }
}
