<?php

namespace App\Providers;

use App\Models\Restaurant;

use App\Observers\RestaurantObserver;

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
        View::composer('*', function ($view) {

            $restaurant = null;

            // только admin
            if (request()->is('admin/*')) {
                $restaurant = AdminContext::actingRestaurant();
            }

            $breadcrumbs = null;

            // breadcrumbs только там где нужны
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
