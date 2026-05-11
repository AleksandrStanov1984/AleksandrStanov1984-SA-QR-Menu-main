<?php

return [

    'zip' => [

        'max_mb' => [
            'user' => 50,
            'super_admin' => 100,
        ],

        'max_files' => [
            'user' => 300,
            'super_admin' => 2000,
        ],

        'max_file_size_mb' => [
            'user' => 15,
            'super_admin' => 15,
        ],

        'allowed_extensions' => [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'svg',
        ],

    ],

];
