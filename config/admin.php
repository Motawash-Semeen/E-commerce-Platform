<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Account
    |--------------------------------------------------------------------------
    |
    | Credentials used by AdminUserSeeder to create the single admin user.
    | Read through config() (not env()) so they still work after config:cache.
    |
    */

    'email' => env('ADMIN_EMAIL', 'admin@example.com'),

    'password' => env('ADMIN_PASSWORD', 'password'),

];
