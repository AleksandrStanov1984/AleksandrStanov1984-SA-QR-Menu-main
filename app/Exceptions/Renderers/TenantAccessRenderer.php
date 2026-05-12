<?php

namespace App\Exceptions\Renderers;

use Illuminate\Http\Request;
use App\Exceptions\TenantAccessException;

class TenantAccessRenderer
{
    public function handle(
        TenantAccessException $e,
        Request $request
    ) {

        $user = $request->user();

        if ($request->expectsJson()) {

            return response()->json([
                'message' => $e->getMessage()
                    ?: __('permissions.no_access'),
            ], 403);
        }

        // =========================
        // SAFE REDIRECT TARGET
        // =========================
        if ($user?->restaurant_id) {

            return redirect()
                ->route(
                    'admin.restaurants.menu',
                    $user->restaurant_id
                )
                ->with(
                    'warning',
                    $e->getMessage()
                    ?: __('permissions.no_access')
                );
        }

        // =========================
        // FALLBACK
        // =========================
        return redirect()
            ->route('admin.home')
            ->with(
                'warning',
                $e->getMessage()
                ?: __('permissions.no_access')
            );
    }
}
