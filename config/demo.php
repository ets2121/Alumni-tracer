<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Demo Data Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains settings for generating demo and beta testing data.
    | Changing these values will affect the scale of the generated dataset
    | when running migrations and seeders.
    |
    */

    'counts' => [
        'verified_per_course' => 10,
        'non_verified_per_course' => 10,
    ],

    'admin' => [
        'domain' => 'qsu.com',
        'default_password' => 'password',
    ],

    'alumni' => [
        'default_password' => 'password',
    ],
];
