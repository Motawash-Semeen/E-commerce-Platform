<?php

// L5Modular settings. Only 'default' is overridden (it replaces the package's
// whole 'default' block, so every key is listed). See
// vendor/artem-schander/l5-modular/src/config/modules.php for the rest.
return [

    'default' => [

        // Modules only have web pages, no API routes.
        'routing' => ['web'],

        'structure' => [
            'controllers' => 'Http/Controllers',
            'resources' => 'Http/Resources',
            'requests' => 'Http/Requests',
            'models' => 'Models',
            'mails' => 'Mail',
            'notifications' => 'Notifications',
            'events' => 'Events',
            'listeners' => 'Listeners',
            'observers' => 'Observers',
            'jobs' => 'Jobs',
            'rules' => 'Rules',
            'views' => 'resources/views',
            'translations' => 'resources/lang',
            'routes' => 'routes',
            'migrations' => 'database/migrations',
            'seeds' => 'database/seeds',

            // L5Modular's factory loader uses a class removed in Laravel 8, so it
            // points at a folder that doesn't exist. Factories live in database/factories.
            'factories' => 'database/legacy-factories-unsupported',

            'helpers' => '',
        ],
    ],

];
