<?php

namespace App\Exceptions\Renderers;

use Illuminate\Http\Request;
use App\Exceptions\TenantAccessException;

class TenantAccessRenderer
{
    public function handle(TenantAccessException $e, Request $request)
    {
        $user = $request->user();

        $resolveFallback = function () use ($request, $user) {
            if ($user && $user->restaurant_id) {
                return route('admin.restaurants.edit', $user->restaurant_id);
            }

            return route('admin.restaurants.index');
        };

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $e->getMessage() ?: __('permissions.no_access'),
            ], 403);
        }

        return redirect()
            ->route('admin.restaurants.menu', $user->restaurant_id)
            ->with('warning', $e->getMessage() ?: __('permissions.no_access'));
    }
}
