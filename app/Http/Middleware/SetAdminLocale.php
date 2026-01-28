<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next)
    {
        $loc = $request->session()->get('admin_locale', 'de');
        if (!in_array($loc, ['de', 'en', 'ru'], true)) {
            $loc = 'de';
        }

        app()->setLocale($loc);

        return $next($request);
    }
}
