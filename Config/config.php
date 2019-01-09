<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of the module.
    |
    */

    'name' => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | Path to the Voyager Assets
    |--------------------------------------------------------------------------
    |
    | Here you can specify the location of the voyager assets path
    |
    */

    'voyager_assets_path' => '/vendor/admin/assets',

    /*
    |--------------------------------------------------------------------------
    | Image manipulation
    |--------------------------------------------------------------------------
    |
    | Image manipulation with Intervention/Image
    |
    */

    'images' => [

        'watermark' => [

            'enabled' => true,
            'path' => public_path('vendor/admin/assets/images/watermark.png'),
            'default_position' => 8,
            'min_height' => 200,
            'min_width' => 200,

        ]
    ],
];
