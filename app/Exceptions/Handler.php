<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\TenantAccessException;
use Illuminate\Auth\Access\AuthorizationException;

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
        //
    }

    public function render($request, Throwable $e)
    {
        /**
         * =========================
         * SAFE FALLBACK RESOLVER
         * =========================
         */
        $resolveFallback = function () use ($request) {
            $user = $request->user();

            // если у пользователя есть ресторан → ведём в его edit
            if ($user && $user->restaurant_id) {
                return route('admin.restaurants.edit', $user->restaurant_id);
            }

            // иначе общий список
            return route('admin.restaurants.index');
        };

        /**
         * =========================
         * TENANT ACCESS
         * =========================
         */
        if ($e instanceof TenantAccessException) {

            if (!$request->expectsJson()) {
                return redirect()
                    ->back(fallback: $resolveFallback())
                    ->with('warning', $e->getMessage() ?: __('permissions.no_access'));
            }

            return response()->json([
                'message' => $e->getMessage() ?: __('permissions.no_access'),
            ], 403);
        }

        /**
         * =========================
         * AUTHORIZATION (FormRequest / Policies)
         * =========================
         */
        if ($e instanceof AuthorizationException) {

            if (!$request->expectsJson()) {
                return redirect()
                    ->back(fallback: $resolveFallback())
                    ->with('warning', $e->getMessage() ?: __('permissions.no_access'));
            }

            return response()->json([
                'message' => $e->getMessage() ?: __('permissions.no_access'),
            ], 403);
        }

        /**
         * =========================
         * DEFAULT
         * =========================
         */
        return parent::render($request, $e);
    }
}
