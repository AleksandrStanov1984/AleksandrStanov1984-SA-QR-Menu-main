<?php

use App\Http\Controllers\Admin\CarouselController;
use App\Http\Controllers\Admin\PromoBannerController;
use App\Http\Controllers\Admin\RestaurantHoursController;
use App\Http\Controllers\Admin\RestaurantLanguageController;
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
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\RestaurantBrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\MenuProfileController;
use App\Http\Controllers\Admin\MenuBuilderController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\MenuImportController;
use App\Http\Controllers\Admin\RestaurantQrController;
use App\Http\Controllers\PlatformLegalController;

use App\Http\Controllers\Public\AuthorController;

use App\ViewModels\PublicMenu\MenuViewModel;

//Route::post('/debug-csrf', function () {
//    return [
//        'session_id' => session()->getId(),
//        'session_token' => session()->token(),
//        'csrf_token()' => csrf_token(),
//        'request__token' => request()->input('_token'),
//    ];
//});

Route::get('/', fn () => redirect()->route('admin.home'));

Route::get('/r/{restaurant:slug}', [PublicMenuController::class, 'show'])
    ->name('restaurant.show');

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::get('/q/{token}', [PublicMenuController::class, 'qr'])->name('qr.resolve');

Route::get('/author/{restaurant}', [AuthorController::class, 'index'])
    ->name('author');

Route::get('/legal/impressum', [PlatformLegalController::class, 'impressum'])
    ->name('platform.legal.impressum');

Route::get('/legal/datenschutz', [PlatformLegalController::class, 'datenschutz'])
    ->name('platform.legal.datenschutz');

Route::get('/r/{restaurant:slug}/impressum', [PublicMenuController::class, 'impressum'])
    ->name('legal.impressum');

Route::get('/r/{restaurant:slug}/datenschutz', [PublicMenuController::class, 'datenschutz'])
    ->name('legal.datenschutz');

Route::post('/locale', function () {
    $locale = request('locale');
    $available = config('locales.all');

    if (in_array($locale, $available)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('locale.set');

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

    Route::post('/locale', function (Request $request) {

        $loc = strtolower(trim((string) $request->input('locale')));

        $available = config('locales.all', ['de']);

        if (!in_array($loc, $available, true)) {
            $loc = config('app.locale', 'de');
        }

        session(['admin_locale' => $loc]);

        app()->setLocale($loc);

        return back();

    })->middleware('auth')->name('locale.set');

    Route::middleware([
        'auth',
        'admin.locale',
        'admin.ensure',
        'admin.restaurant'
    ])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('home');

        Route::resource('restaurants', RestaurantController::class)->except(['show']);

        Route::post('/select-restaurant', [DashboardController::class, 'selectRestaurant'])
            ->name('select_restaurant');

        Route::post('restaurants/{restaurant}/toggle', [RestaurantController::class, 'toggleActive'])
            ->name('restaurants.toggle');


        // PERMISSIONS
        Route::post('restaurants/{restaurant}/user-permissions', [RestaurantController::class, 'updateUserPermissions'])
            ->name('restaurants.user_permissions');

        Route::get('restaurants/{restaurant}/permissions', [RestaurantController::class, 'permissions'])
            ->name('restaurants.permissions');


        // LANGUAGES
        Route::post('restaurants/{restaurant}/languages/import', [LanguageImportController::class, 'import'])
            ->name('restaurants.languages.import');

        Route::post('restaurants/{restaurant}/languages/default', [LanguageImportController::class, 'setDefault'])
            ->name('restaurants.languages.default');

        // PROFILE
        Route::get('restaurants/{restaurant}/profile', [RestaurantController::class, 'profile'])
            ->name('restaurants.profile');

        Route::get('/profile', [ProfileController::class, 'show'])
            ->name('profile');

        Route::post('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::post('restaurants/{restaurant}/profile', [ProfileController::class, 'updateRestaurant'])
            ->name('restaurants.profile.update');

        Route::post('/profile/restaurant', [ProfileController::class, 'updateRestaurant'])
            ->name('profile.restaurant.update');

        // SECURITY ADMIN
        Route::get('/security/login', [ProfileCredentialsController::class, 'showAdminLogin'])
            ->name('security.login');

        Route::get('/security/password', [ProfileCredentialsController::class, 'showAdminPassword'])
            ->name('security.password');

        Route::post('/profile/change-email', [ProfileCredentialsController::class, 'changeEmail'])
            ->name('profile.change_email');

        Route::post('/profile/change-password', [ProfileCredentialsController::class, 'changePassword'])
            ->name('profile.change_password');

        //  SECURITY (USER)
        Route::get('restaurants/{restaurant}/credentials', [ProfileCredentialsController::class, 'showRestaurantCredentials'])
            ->name('restaurants.credentials');

        Route::get('restaurants/{restaurant}/credentials/login', [ProfileCredentialsController::class, 'showRestaurantLogin'])
            ->name('restaurants.credentials.login');

        Route::post('restaurants/{restaurant}/credentials/email', [ProfileCredentialsController::class, 'changeRestaurantEmail'])
            ->name('restaurants.credentials.email');

        Route::post('restaurants/{restaurant}/credentials/password', [ProfileCredentialsController::class, 'changeRestaurantPassword'])
            ->name('restaurants.credentials.password');

        // BRANDING
        Route::post('restaurants/{restaurant}/logo', [RestaurantBrandController::class, 'update'])
            ->name('restaurants.logo.update');

        Route::delete('restaurants/{restaurant}/logo', [RestaurantBrandController::class, 'delete'])
            ->name('restaurants.logo.delete');

        Route::post('restaurants/{restaurant}/branding/backgrounds', [RestaurantBrandController::class, 'updateBackgrounds'])
            ->name('restaurants.branding.backgrounds.update');

        Route::get('restaurants/{restaurant}/branding', [RestaurantBrandController::class, 'edit'])
            ->name('restaurants.branding');

        Route::delete('restaurants/{restaurant}/branding/backgrounds/{type}', [RestaurantBrandController::class, 'deleteBackground']
        )->name('restaurants.branding.backgrounds.delete');

        // OG
        Route::post('/restaurants/{restaurant}/branding/og', [RestaurantBrandController::class, 'uploadOg']
        )->name('restaurants.og.upload');

        Route::delete('/restaurants/{restaurant}/branding/og/{locale}', [RestaurantBrandController::class, 'deleteOg']
        )->name('restaurants.og.delete');

        // SECTIONS
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

        Route::delete('restaurants/{restaurant}/sections/{section}', [SectionController::class, 'destroy'])
            ->name('restaurants.sections.destroy');

        // CATEGORIES / SUBCATEGORIES
        Route::get('restaurants/{restaurant}/menu', [RestaurantController::class, 'menu'])
            ->name('restaurants.menu');

        Route::post('restaurants/{restaurant}/categories', [CategoryController::class, 'store'])
            ->name('restaurants.categories.store');

        Route::post('restaurants/{restaurant}/subcategories', [SubcategoryController::class, 'store'])
            ->name('restaurants.subcategories.store');

        // Menu ITEMS
        Route::post('restaurants/{restaurant}/sections/{section}/items', [ItemController::class, 'store'])
            ->name('restaurants.items.store');

        Route::put('restaurants/{restaurant}/items/{item}', [ItemController::class, 'update'])
            ->name('restaurants.items.update');

        Route::post('/restaurants/{restaurant}/sections/{section}/items/reorder', [ItemController::class, 'reorder']
        )->name('restaurants.items.reorder');

        Route::delete('restaurants/{restaurant}/items/{item}', [ItemController::class, 'destroy'])
            ->name('restaurants.items.destroy');

        Route::patch('restaurants/{restaurant}/items/{item}/active', [ItemController::class, 'updateActive'])
            ->name('restaurants.items.active');

        Route::patch('restaurants/{restaurant}/items/{item}/meta', [ItemController::class, 'updateMeta'])
            ->name('restaurants.items.meta');

        Route::delete('restaurants/{restaurant}/items/{item}/image', [ItemController::class, 'deleteImage']
        )->name('restaurants.items.image.delete');

        // Menu Profile
        Route::get('/menu/profile', [MenuProfileController::class, 'edit'])
            ->name('menu.profile');

        // SOCIALS
        Route::post('restaurants/{restaurant}/social-links', [SocialLinkController::class, 'store'])
            ->name('restaurants.social_links.store');

        Route::put('restaurants/{restaurant}/social-links/{link}', [SocialLinkController::class, 'update'])
            ->name('restaurants.social_links.update');

        Route::delete('restaurants/{restaurant}/social-links/{link}', [SocialLinkController::class, 'destroy'])
            ->name('restaurants.social_links.destroy');

        Route::patch('restaurants/{restaurant}/social-links/{link}/toggle-active', [SocialLinkController::class, 'toggleActive'])
            ->name('restaurants.social_links.toggle_active');

        Route::get('restaurants/{restaurant}/socials', [SocialLinkController::class, 'index'])
            ->name('restaurants.socials');

        Route::post('restaurants/{restaurant}/social-links/reorder', [SocialLinkController::class, 'reorder']
        )->name('restaurants.social_links.reorder');

        Route::delete('restaurants/{restaurant}/social-links/{link}/icon', [SocialLinkController::class, 'removeIcon'])
            ->name('restaurants.social_links.remove_icon');

        // Author
        Route::get('/about', [AboutController::class, 'index'])
            ->name('about');

        // MENU IMPORT / EXPORT
        Route::post('restaurants/{restaurant}/menu/import-json', [MenuImportController::class, 'importJson'])
            ->name('restaurants.menu.import_json');

        Route::post('restaurants/{restaurant}/menu/import-zip', [MenuImportController::class, 'importZip'])
            ->name('restaurants.menu.import_zip');

        Route::get('restaurants/{restaurant}/menu/import-log/{token}', [MenuImportController::class, 'downloadLog'])
            ->name('restaurants.menu.import_log');

        Route::get('restaurants/{restaurant}/menu/export', [MenuImportController::class, 'downloadMenuJson'])
            ->name('restaurants.menu.export_json');

        // IMPORT UI
        Route::get('restaurants/{restaurant}/import', [MenuImportController::class, 'index'])
            ->name('restaurants.import');

        Route::get('restaurants/{restaurant}/import/images', [MenuImportController::class, 'images'])
            ->name('restaurants.import.images');

        Route::get(
            'restaurants/{restaurant}/menu/import-status',
            [MenuImportController::class, 'status']
        )->name('restaurants.menu.import_status');

        Route::get(
            'restaurants/{restaurant}/menu/import-unmatched',
            [MenuImportController::class, 'downloadUnmatched']
        )->name('restaurants.menu.import_unmatched');

        // HOURS
        Route::post('restaurants/{restaurant}/hours', [RestaurantHoursController::class, 'update'])
            ->name('restaurants.hours.update');

        Route::get('restaurants/{restaurant}/hours', [RestaurantHoursController::class, 'edit'])
            ->name('restaurants.hours');

        // BANNERS
        Route::get('restaurants/{restaurant}/banners', [PromoBannerController::class, 'index'])
            ->name('restaurants.banners.index')
            ->defaults('plan', 'pro');

        Route::post('restaurants/{restaurant}/banners/save', [PromoBannerController::class, 'save'])
            ->name('restaurants.banners.save')
            ->defaults('plan', 'pro');

        Route::post('restaurants/{restaurant}/banners/reorder', [PromoBannerController::class, 'reorder'])
            ->name('restaurants.banners.reorder')
            ->defaults('plan', 'pro');

        Route::delete('restaurants/{restaurant}/banners/{id}', [PromoBannerController::class, 'destroy'])
            ->name('restaurants.banners.destroy')
            ->defaults('plan', 'pro');

        Route::delete('restaurants/{restaurant}/banners', [PromoBannerController::class, 'destroyAll'])
            ->name('restaurants.banners.destroyAll')
            ->defaults('plan', 'pro');

        // Carousel
        Route::get('/admin/restaurants/{restaurant}/carousel', [CarouselController::class, 'index']
        )->name('restaurants.carousel');

        Route::post('/restaurants/{restaurant}/carousel', [CarouselController::class, 'update'])
            ->name('restaurants.carousel.update');

        // QR
        Route::post('restaurants/{restaurant}/qr/generate', [RestaurantQrController::class, 'generate'])
            ->name('restaurants.qr.generate');

        Route::get('restaurants/{restaurant}/qr/download/{format}', [RestaurantQrController::class, 'download'])
            ->name('restaurants.qr.download');

        Route::get('restaurants/{restaurant}/qr', [RestaurantQrController::class, 'index'])
            ->name('restaurants.qr');

        Route::delete('restaurants/{restaurant}/qr/logo', [RestaurantQrController::class, 'deleteLogo'])
            ->name('restaurants.qr.deleteLogo');

        Route::delete('restaurants/{restaurant}/qr/background', [RestaurantQrController::class, 'deleteBackground'])
            ->name('restaurants.qr.deleteBackground');

        // languages
        Route::get('restaurants/{restaurant}/languages', [RestaurantLanguageController::class, 'index']
        )->name('restaurants.languages');

        Route::post('restaurants/{restaurant}/languages', [RestaurantLanguageController::class, 'update']
        )->name('restaurants.languages.update');


    });

});
