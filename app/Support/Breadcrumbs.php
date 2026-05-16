<?php

namespace App\Support;

use Illuminate\Http\Request;
use App\Models\Restaurant;

class Breadcrumbs
{
    public static function make(Request $request): array
    {
        $route = $request->route()?->getName();
        $restaurant = $request->route('restaurant');
        $user = auth()->user();
        $isSuper = (bool)($user?->is_super_admin);

        // =============================
        // SUPER ADMIN — БЕЗ ОБЪЕКТА
        // =============================
        if ($isSuper && !$restaurant) {
            return [
                [
                    'label' => __('admin.restaurants.index.h1'),
                ],
            ];
        }

        // =============================
        // НЕТ объекта — обычный пользователь
        // =============================
        if (!$restaurant instanceof Restaurant) {
            return [];
        }

        // =============================
        // BASE
        // =============================
        if ($isSuper) {
            $base = [
                [
                    'label' => __('admin.restaurants.index.h1'),
                    'url'   => route('admin.restaurants.index'),
                ],
                [
                    'label' => $restaurant->name,
                    'url'   => route('admin.restaurants.profile', $restaurant),
                ],
            ];
        } else {
            $base = [
                [
                    'label' => $restaurant->name,
                    'url'   => route('admin.restaurants.profile', $restaurant),
                ],
            ];
        }

        // =============================
        // ROUTES
        // =============================
        return match (true) {

            str_starts_with($route, 'admin.restaurants.profile') => [
                ...$base,
                ['label' => __('admin.sidebar.profile')],
            ],

            $route === 'admin.restaurants.hours' => [
                ...$base,
                ['label' => __('admin.sidebar.hours')],
            ],

            str_starts_with($route, 'admin.restaurants.menu') => [
                ...$base,
                ['label' => __('admin.sidebar.menu')],
            ],

            $route === 'admin.restaurants.branding' => [
                ...$base,
                ['label' => __('admin.sidebar.branding')],
            ],

            $route === 'admin.restaurants.qr' => [
                ...$base,
                ['label' => __('admin.sidebar.qr')],
            ],

            $route === 'admin.restaurants.socials' => [
                ...$base,
                ['label' => __('admin.sidebar.socials')],
            ],

            str_starts_with($route, 'admin.restaurants.banners') => [
                ...$base,
                ['label' => __('admin.sidebar.banners')],
            ],

            str_starts_with($route, 'admin.restaurants.carousel') => [
                ...$base,
                ['label' => __('admin.sidebar.carousel')],
            ],

            $route === 'admin.restaurants.credentials' => [
                ...$base,
                ['label' => __('admin.sidebar.password')],
            ],

            $route === 'admin.restaurants.permissions' => [
                ...$base,
                ['label' => __('admin.sidebar.permissions')],
            ],

            // BILLING
            str_starts_with($route, 'admin.restaurants.billing') => [
                ...$base,
                ['label' => __('admin.sidebar.billing')],
            ],

            // LANGUAGES
            str_starts_with($route, 'admin.restaurants.languages') => [
                ...$base,
                ['label' => __('admin.sidebar.languages')],
            ],

            // IMPORT
            str_starts_with($route, 'admin.restaurants.import') => [
                ...$base,
                ['label' => __('admin.sidebar.import')],
            ],

            str_starts_with($route, 'admin.restaurants.menu.import_') => [
                ...$base,
                ['label' => __('admin.sidebar.import')],
            ],

            // SECURITY (USER)
            str_starts_with($route, 'admin.restaurants.credentials') => [
                ...$base,
                ['label' => __('admin.sidebar.password')],
            ],

            // SECURITY (ADMIN)
            str_starts_with($route, 'admin.security.') => [
                [
                    'label' => __('admin.sidebar.security'),
                ],
            ],

            default => $base,
        };
    }
}
