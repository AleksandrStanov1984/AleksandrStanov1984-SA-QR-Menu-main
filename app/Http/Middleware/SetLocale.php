<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // ❗ НИЧЕГО НЕ ДЕЛАЕМ С LOCALE
        // Locale полностью управляется через PublicMenuController (restaurant-aware)

        return $next($request);
    }
}
