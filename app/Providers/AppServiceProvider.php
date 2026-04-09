<?php

namespace App\Providers;

use App\Support\AdminContext;
use App\Support\Breadcrumbs;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
