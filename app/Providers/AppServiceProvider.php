<?php

namespace App\Providers;

use App\Models\Restaurant;
use App\Observers\RestaurantObserver;
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
        // 🔥 observer
        Restaurant::observe(RestaurantObserver::class);

        // GLOBAL VIEW CONTEXT
        View::composer('*', function ($view) {

            // текущий ресторан (контекст)
            $restaurant = AdminContext::restaurant();

            // breadcrumbs
            $breadcrumbs = Breadcrumbs::make(request());

            $view->with([
                'restaurant' => $restaurant,
                'breadcrumbs' => $breadcrumbs,
            ]);
        });
    }
}
