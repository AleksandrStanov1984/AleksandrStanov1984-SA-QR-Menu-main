<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\PublicMenuController;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\LanguageImportController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProfileCredentialsController;
use App\Http\Controllers\Admin\SubcategoryController;

use App\Http\Controllers\Admin\RestaurantBrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\MenuProfileController;
use App\Http\Controllers\Admin\MenuBuilderController;



Route::get('/q/{token}', [PublicMenuController::class, 'qr'])->name('qr.resolve');

Route::get('/r/{restaurant:slug}', [PublicMenuController::class, 'show'])->name('restaurant.show');

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');


Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('guest')
        ->name('login.submit');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // ✅ locale setter (admin)
    Route::post('/locale', function (Request $request) {

        $loc = strtolower(trim((string) $request->input('locale')));

        if (!in_array($loc, ['de', 'en', 'ru'], true))
         $loc = 'de';

        session(['admin_locale' => $loc]);

        app()->setLocale($loc);

        return redirect()->back();

    })->middleware('auth')->name('locale.set');

    Route::middleware(['auth', 'admin.locale', 'admin.ensure', 'admin.restaurant'])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('home');

        Route::post('/select-restaurant', [DashboardController::class, 'selectRestaurant'])->name('select_restaurant');

        Route::resource('restaurants', RestaurantController::class)->except(['show']);

        Route::post('restaurants/{restaurant}/toggle', [RestaurantController::class, 'toggleActive'])->name('restaurants.toggle');

        Route::post('restaurants/{restaurant}/user-permissions', [RestaurantController::class, 'updateUserPermissions'])
            ->name('restaurants.user_permissions');

        Route::post('restaurants/{restaurant}/languages/import', [LanguageImportController::class, 'import'])
            ->name('restaurants.languages.import');

        Route::post('restaurants/{restaurant}/languages/default', [LanguageImportController::class, 'setDefault'])
            ->name('restaurants.languages.default');

        Route::get('restaurants/{restaurant}/sections', [SectionController::class, 'index'])
            ->name('restaurants.sections.index');

        Route::post('restaurants/{restaurant}/sections', [SectionController::class, 'store'])
            ->name('restaurants.sections.store');

        Route::get('restaurants/{restaurant}/sections/{section}/edit', [SectionController::class, 'edit'])
            ->name('restaurants.sections.edit');

        Route::put('restaurants/{restaurant}/sections/{section}', [SectionController::class, 'update'])
            ->name('restaurants.sections.update');

        Route::post('restaurants/{restaurant}/sections/{section}/toggle', [SectionController::class, 'toggleActive'])
            ->name('restaurants.sections.toggle');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // restaurant contact/address (edited from Profile screen)
        Route::post('/profile/restaurant', [ProfileController::class, 'updateRestaurant'])
            ->name('profile.restaurant.update');

        Route::post('/profile/change-email', [ProfileCredentialsController::class, 'changeEmail'])
            ->name('profile.change_email');

        Route::post('/profile/change-password', [ProfileCredentialsController::class, 'changePassword'])
            ->name('profile.change_password');

        Route::post('restaurants/{restaurant}/logo', [RestaurantBrandController::class, 'update'])
            ->name('restaurants.logo.update');

        Route::post('restaurants/{restaurant}/categories', [CategoryController::class, 'store'])
            ->name('restaurants.categories.store');

        Route::post('restaurants/{restaurant}/sections/{section}/items', [ItemController::class, 'store'])
            ->name('restaurants.items.store');

        Route::put('restaurants/{restaurant}/items/{item}', [ItemController::class, 'update'])
            ->name('restaurants.items.update');

        Route::post('restaurants/{restaurant}/sections/{section}/items/reorder', [ItemController::class, 'reorder'])
            ->name('restaurants.items.reorder');

        Route::post('restaurants/{restaurant}/subcategories', [SubcategoryController::class, 'store'])
            ->name('restaurants.subcategories.store');

        Route::delete('restaurants/{restaurant}/sections/{section}', [SectionController::class, 'destroy'])
            ->name('restaurants.sections.destroy');

        Route::delete('restaurants/{restaurant}/items/{item}', [ItemController::class, 'destroy'])
            ->name('restaurants.items.destroy');

        Route::post('restaurants/{restaurant}/items/{item}/toggle', [ItemController::class, 'toggleActive'])
            ->name('restaurants.items.toggle');

        Route::get('/menu/profile', [MenuProfileController::class, 'edit'])
            ->name('menu.profile');

        Route::get('/security/password', function (\Illuminate\Http\Request $request) {
            return view('admin.security.password', [
                'user' => $request->user(),
            ]);
        })->name('security.password');

        Route::post('restaurants/{restaurant}/branding/backgrounds', [RestaurantBrandController::class, 'updateBackgrounds'])
            ->name('restaurants.branding.backgrounds.update');








    });


});
