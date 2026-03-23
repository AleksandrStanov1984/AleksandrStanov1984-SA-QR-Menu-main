<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image driver
    |--------------------------------------------------------------------------
    */

    'driver' => 'pipeline',

    /*
    |--------------------------------------------------------------------------
    | Filesystem paths
    |--------------------------------------------------------------------------
    */

    'paths' => [

        'inbox' => storage_path('app/image-inbox'),

        'assets' => public_path('assets'),

        'reports' => storage_path('app/reports'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Public URLs
    |--------------------------------------------------------------------------
    */

    'urls' => [

        'assets' => '/assets',

        'fallback' => '/assets/system/fallback/food.webp',

    ],

    /*
    |--------------------------------------------------------------------------
    | System assets (global images)
    |--------------------------------------------------------------------------
    */

    'system' => [
        'author' => 'system/author/author.webp',
        'social_icons' => 'system/social',
        'icons' => 'system/icons',

        'fallbacks' => [
            'food' => 'system/fallback/food.webp',
            'logo' => 'system/fallback/logo.webp',
        ],
        'qr'   => 'system/qr/fallback.webp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Restaurant assets structure
    |--------------------------------------------------------------------------
    */

    'restaurant' => [

        'base' => 'restaurants',

        'directories' => [

            'logo'       => 'logo',
            'hero'       => 'hero',
            'menu_items' => 'menu/items',
            'categories' => 'categories',
            'gallery'    => 'gallery',

            'qr'         => 'qr',
            'qr_logo'    => 'qr/logo',
            'qr_bg'      => 'qr/background',
            'qr_final'   => 'qr/final',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Image optimization profiles
    |--------------------------------------------------------------------------
    */

    'profiles' => [

        'logo' => [
            'width' => 400,
            'retina' => false,
            'webp' => true,
        ],

        'hero' => [
            'width' => 1600,
            'retina' => true,
            'webp' => true,
        ],

        'menu_item' => [
            'width' => 1200,
            'retina' => true,
            'webp' => true,
        ],

        'category' => [
            'width' => 800,
            'retina' => true,
            'webp' => true,
        ],

        'gallery' => [
            'width' => 1600,
            'retina' => true,
            'webp' => true,
        ],

        'thumbnail' => [
            'width' => 400,
            'retina' => false,
            'webp' => true,
        ],

    ],

];
