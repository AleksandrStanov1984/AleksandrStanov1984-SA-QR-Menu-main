<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next)
    {
        $availableLocales = config('locales.all', ['de']);

        $loc = $request->session()->get('admin_locale', $availableLocales[0]);

        if (!in_array($loc, $availableLocales, true)) {
            $loc = $availableLocales[0];
        }

        app()->setLocale($loc);

        return $next($request);
    }
}
