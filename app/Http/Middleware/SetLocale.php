<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $available = config('locales.all', ['de']);
        $default   = config('locales.default', 'de');

        $locale = $request->get('lang');

        // если язык из URL невалидный → игнорим
        if (!in_array($locale, $available)) {
            $locale = session('locale', $default);
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
