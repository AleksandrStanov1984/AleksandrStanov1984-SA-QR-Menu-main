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

        $base = [
            [
                'label' => __('admin.dashboard.home'),
                'url' => route('admin.home'),
            ],
        ];

        // список ресторанов
        if ($route === 'admin.restaurants.index') {
            return [
                ...$base,
                [
                    'label' => __('admin.restaurants.index.h1'),
                ],
            ];
        }

        // внутри ресторана
        if ($restaurant instanceof Restaurant) {

            $crumbs = [
                ...$base,
                [
                    'label' => __('admin.restaurants.index.h1'),
                    'url' => route('admin.restaurants.index'),
                ],
                [
                    'label' => $restaurant->name,
                    'url' => route('admin.restaurants.edit', $restaurant),
                ],
            ];

            return match ($route) {

                'admin.restaurants.hours' => [
                    ...$crumbs,
                    ['label' => __('admin.sidebar.hours')],
                ],

                'admin.restaurants.branding' => [
                    ...$crumbs,
                    ['label' => __('admin.sidebar.branding')],
                ],

                'admin.restaurants.qr' => [
                    ...$crumbs,
                    ['label' => 'QR-код'],
                ],

                'admin.restaurants.socials' => [
                    ...$crumbs,
                    ['label' => __('admin.sidebar.socials')],
                ],

                'admin.restaurants.import' => [
                    ...$crumbs,
                    ['label' => __('admin.sidebar.import_menu')],
                ],

                default => $crumbs,
            };
        }

        return $base;
    }
}
