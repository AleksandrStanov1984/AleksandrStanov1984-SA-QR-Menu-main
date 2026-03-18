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
        'retina' => true,
        'clean_names' => false,
        'hash_names' => false,
        'purge_webp' => false,
        'delete_sources' => true,
    ],

    'cron' => [
        'move_strategy' => 'copy_then_delete',
        'allow_ext' => ['jpg', 'jpeg', 'png', 'webp'],
        'ignore_hidden' => true,
        'fingerprint' => 'size_mtime',
        'max_source_mb' => 40,
    ],

    /*
    |--------------------------------------------------------------------------
    | Profiles (CRITICAL: matcher читает отсюда)
    |--------------------------------------------------------------------------
    */

    'profiles' => [

        'restaurant_logo' => [
            'name' => 'restaurant_logo',
            'match' => 'restaurants/*/logo/*',
            'format' => 'webp',
            'quality' => 82,
            'sizes' => [600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'restaurant_hero' => [
            'name' => 'restaurant_hero',
            'match' => 'restaurants/*/hero/*',
            'format' => 'webp',
            'quality' => 84,
            'sizes' => [1600],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'menu_item' => [
            'name' => 'menu_item',
            'match' => 'restaurants/*/menu/items/*',
            'format' => 'webp',
            'quality' => 84,
            'sizes' => [1200],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'category' => [
            'name' => 'category',
            'match' => 'restaurants/*/categories/*',
            'format' => 'webp',
            'quality' => 82,
            'sizes' => [800],
            'hash_names' => false,
            'keep_source' => false,
        ],

        'gallery' => [
            'name' => 'gallery',
            'match' => 'restaurants/*/gallery/*',
            'format' => 'webp',
            'quality' => 84,
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

    ],

];
