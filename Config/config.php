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
    | Image manipulation
    |--------------------------------------------------------------------------
    |
    | This value is the name of the module.
    |
    */

    'images' => [
        'watermark' => [
            'enabled' => true,
            'path' => public_path('images/watermark.png'),
            'default_position' => 8,
            'min_height' => 200,
            'min_width' => 200,
        ]
    ],
];
