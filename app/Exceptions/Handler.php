<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\TenantAccessException;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        /**
         * =========================
         * TENANT ACCESS
         * =========================
         */
        $this->renderable(function (TenantAccessException $e, $request) {

            $resolveFallback = function () use ($request) {
                $user = $request->user();

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
                ->to(url()->previous() ?: $resolveFallback())
                ->with('warning', $e->getMessage() ?: __('permissions.no_access'));
        });

        /**
         * =========================
         * AUTHORIZATION
         * =========================
         */
        $this->renderable(function (AuthorizationException $e, $request) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: __('permissions.no_access'),
                ], 403);
            }

            return redirect()
                ->back()
                ->with('warning', $e->getMessage() ?: __('permissions.no_access'));
        });
    }
}
