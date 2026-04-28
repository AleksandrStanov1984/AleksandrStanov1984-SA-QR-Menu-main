<?php

return [

    'inbox_dir' => 'image-inbox',
    'inbox_expected_prefix' => 'assets',
    'manifest_path' => 'image-inbox/.manifest.json',
    'reports_dir' => 'reports',

    'paths' => [
        'inbox' => storage_path('app/image-inbox'),
        'assets' => public_path('assets'),
        'reports' => storage_path('app/reports'),
    ],

    'urls' => [
        'assets' => '/assets',
        'fallback' => '/assets/system/fallback/food.webp',
    ],

    'default_optimize_args' => [
        'retina' => false,
        'clean_names' => false,
        'hash_names' => false,
        'purge_webp' => false,
        'delete_sources' => true,
    ],

    'cron' => [
        'move_strategy' => 'copy_then_delete',
        'allow_ext' => ['jpg', 'jpeg', 'png', 'webp'],
        'ignore_hidden' => true,
        'fingerprint' => null,
        'max_source_mb' => 40,
    ],

    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    */

    'profiles' => [

        'restaurant_logo' => [
            'name' => 'restaurant_logo',
            'match' => 'restaurants/*/branding/logo/*',
            'format' => 'webp',
            'quality' => 85,
            'sizes' => [600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'restaurant_hero' => [
            'name' => 'restaurant_hero',
            'match' => 'restaurants/*/hero/*',
            'format' => 'webp',
            'quality' => 74,
            'sizes' => [1600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'menu_item' => [
            'name' => 'menu_item',
            'match' => 'restaurants/*/menu/items/*',
            'format' => 'webp',
            'quality' => 78,
            'sizes' => [1200],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'category' => [
            'name' => 'category',
            'match' => 'restaurants/*/categories/*',
            'format' => 'webp',
            'quality' => 74,
            'sizes' => [800],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'gallery' => [
            'name' => 'gallery',
            'match' => 'restaurants/*/gallery/*',
            'format' => 'webp',
            'quality' => 76,
            'sizes' => [1600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'author' => [
            'name' => 'author',
            'match' => 'system/author/*',
            'format' => 'webp',
            'quality' => 84,
            'sizes' => [800],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'system_banner' => [
            'name' => 'system_banner',
            'match' => 'system/banners/*',
            'format' => 'webp',
            'quality' => 86,
            'sizes' => [1200],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'banner' => [
            'name' => 'banner',
            'match' => 'restaurants/*/banners/*',
            'format' => 'webp',
            'quality' => 72,
            'sizes' => [400, 800, 1200],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'branding_bg_light' => [
            'name' => 'branding_bg_light',
            'match' => 'restaurants/*/branding/backgrounds/light/*',
            'format' => 'webp',
            'quality' => 86,
            'sizes' => [1600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'branding_bg_dark' => [
            'name' => 'branding_bg_dark',
            'match' => 'restaurants/*/branding/backgrounds/dark/*',
            'format' => 'webp',
            'quality' => 86,
            'sizes' => [1600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'branding_og' => [
            'name' => 'branding_og',
            'match' => 'restaurants/*/branding/og/*/*',
            'format' => 'webp',
            'quality' => 82,
            'exact' => [
                'w' => 1200,
                'h' => 630,
            ],
            'hash_names' => false,
            'keepSource' => false,
        ],

        'system_og' => [
            'name' => 'system_og',
            'match' => 'system/og/*/*',
            'format' => 'webp',
            'quality' => 86,
            'exact' => [
                'w' => 1200,
                'h' => 630,
            ],
            'keepSource' => false,
        ],

        'system_og_root' => [
            'name' => 'system_og_root',
            'match' => 'system/og/*',
            'format' => 'webp',
            'quality' => 86,
            'exact' => [
                'w' => 1200,
                'h' => 630,
            ],
            'keepSource' => false,
        ],

        'system_fallback' => [
            'name' => 'system_fallback',
            'match' => 'system/fallback/*',
            'format' => 'webp',
            'quality' => 82,
            'sizes' => [800],
            'hash_names' => false,
            'keep_source' => true,
        ],

    ],

    'qr' => [
        'name' => 'qr',
        'match' => 'restaurants/*/qr/*',
        'format' => 'webp',
        'quality' => 90,
        'sizes' => [800],
        'hash_names' => false,
        'keep_source' => false,
    ],

    'system_qr' => [
        'name' => 'system_qr',
        'match' => 'system/qr/*',
        'format' => 'webp',
        'quality' => 90,
        'sizes' => [800],
        'hash_names' => false,
        'keep_source' => false,
    ],
];
