<?php

namespace App\Providers;

use App\Support\AdminContext;
use App\Support\Breadcrumbs;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {

            $restaurant = null;

            if (request()->is('admin/*')) {
                $restaurant = AdminContext::actingRestaurant();
            }

            $breadcrumbs = null;

            if (request()->is('admin/*')) {
                $breadcrumbs = Breadcrumbs::make(request());
            }

            $view->with([
                'ctxRestaurant' => $restaurant,
                'breadcrumbs'   => $breadcrumbs,
            ]);
        });
    }
}
