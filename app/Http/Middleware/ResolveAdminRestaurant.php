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

        // обычный пользователь
        if (!$user->is_super_admin) {

            if ($user->restaurant_id) {
                $currentRestaurant = Restaurant::find($user->restaurant_id);

                // синхронизируем session (важно)
                session(['admin.restaurant_id' => $user->restaurant_id]);
            }

        } else {

            //  ROUTE приоритет
            $routeRestaurant = $request->route('restaurant');

            if ($routeRestaurant instanceof Restaurant) {
                $currentRestaurant = $routeRestaurant;

                // сохраняем в session
                session(['admin.restaurant_id' => $routeRestaurant->id]);

            } else {

                //fallback → session
                $id = session('admin.restaurant_id');

                if ($id) {
                    $currentRestaurant = Restaurant::where('id', $id)
                        ->where('is_active', true)
                        ->first();
                }
            }
        }

        // прокидываем в request
        $request->attributes->set('admin_restaurant', $currentRestaurant);

        return $next($request);
    }
}
