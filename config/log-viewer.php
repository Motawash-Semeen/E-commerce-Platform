<?php

use Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer;

// Only the settings changed from the package defaults
// (see vendor/opcodesio/log-viewer/config/log-viewer.php for the rest).
return [

    'route_path' => 'admin/log-viewer',

    'back_to_system_url' => '/admin/dashboard',
    'back_to_system_label' => 'Back to admin',

    // Admin only: guests are sent to the login page, shop owners get 403.
    'middleware' => ['web', 'auth', 'role:admin', AuthorizeLogViewer::class],

    // The page loads its data from this API. Plain 'web' sessions work on any
    // host (the package default only trusts a fixed list such as 127.0.0.1:8000).
    'api_middleware' => ['web', 'auth', 'role:admin', AuthorizeLogViewer::class],

];
