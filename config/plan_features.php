<?php

return [

    'starter' => [

        // ===== CORE FEATURES =====
        'images' => false,
        'spicy' => false,
        'dish_of_day' => false,
        'is_new' => false,
        'bestseller' => false,
        'banners' => false,
        'custom_background' => false,
        'long_description' => false,

        // ===== UI / BEHAVIOR =====
        'item_modal' => true,
        'status' => false,
        'search' => false,
        'hours_modal' => false,

        // ===== SOCIAL =====
        'social_limit' => 1,

        // ===== CAROUSEL =====
        'carousel' => false,
        'carousel_advanced' => false,
    ],

    'basic' => [

        // ===== CORE FEATURES =====
        'images' => true,
        'spicy' => true,
        'dish_of_day' => false,
        'is_new' => false,
        'bestseller' => true,
        'banners' => false,
        'custom_background' => false,
        'long_description' => false,

        // ===== UI / BEHAVIOR =====
        'item_modal' => true,
        'status' => true,
        'search' => true,
        'hours_modal' => false,

        // ===== SOCIAL =====
        'social_limit' => 3,

        // ===== CAROUSEL =====
        'carousel' => true,
        'carousel_advanced' => false,
    ],

    'pro' => [

        // ===== CORE FEATURES =====
        'images' => true,
        'spicy' => true,
        'dish_of_day' => true,
        'is_new' => true,
        'bestseller' => true,
        'banners' => true,
        'custom_background' => true,
        'long_description' => true,

        // ===== UI / BEHAVIOR =====
        'item_modal' => true,
        'status' => true,
        'search' => true,
        'hours_modal' => true,

        // ===== SOCIAL =====
        'social_limit' => 5,

        // ===== CAROUSEL =====
        'carousel' => true,
        'carousel_advanced' => true,
    ],

];
