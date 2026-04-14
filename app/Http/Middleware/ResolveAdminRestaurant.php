<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveAdminRestaurant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        $currentRestaurant = null;

        // =========================
        // 👤 USER
        // =========================
        if (!$user->is_super_admin) {

            if ($user->restaurant_id) {
                $currentRestaurant = Restaurant::find($user->restaurant_id);

                if ($currentRestaurant) {
                    \App\Support\AdminContext::setActingRestaurant($currentRestaurant);
                }
            }

        } else {

            // =========================
            // ADMIN
            // =========================

            $routeRestaurant = $request->route('restaurant');

            if ($routeRestaurant instanceof Restaurant) {
                $currentRestaurant = $routeRestaurant;

                \App\Support\AdminContext::setActingRestaurant($routeRestaurant);

            } else {

                // fallback session
                $currentRestaurant = \App\Support\AdminContext::actingRestaurant();
            }
        }

        $request->attributes->set('admin_restaurant', $currentRestaurant);

        return $next($request);
    }
}
