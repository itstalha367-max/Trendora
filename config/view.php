<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Blade templates are loaded from the standard resources/views directory.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Do not wrap the default path in realpath(). On a fresh ZIP extraction the
    | framework/views directory may not exist yet, which makes realpath() return
    | false and causes `php artisan optimize:clear` / `view:clear` to fail with
    | "View path not found." storage_path() is safe even before compilation.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),

];
