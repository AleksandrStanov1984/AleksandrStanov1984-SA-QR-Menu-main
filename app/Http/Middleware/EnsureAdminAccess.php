<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    /**
     * For MVP we treat ANY authenticated user as an "admin user".
     * Super-admin behaviour is controlled by `users.is_super_admin`.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
