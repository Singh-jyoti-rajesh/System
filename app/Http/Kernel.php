<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    // protected $routeMiddleware = [
    //     // other middleware...

    //     'admin' => \App\Http\Middleware\AdminOnly::class,
    // ];
    protected $routeMiddleware = [
        // ...
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
}
