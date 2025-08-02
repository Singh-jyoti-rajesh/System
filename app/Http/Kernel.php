<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // protected $routeMiddleware = [
    //     // ...
    //     'admin' => \App\Http\Middleware\AdminOnly::class, // ✅ must point to the correct class
    // ];
    protected $routeMiddleware = [
        // other middleware...

        'admin' => \App\Http\Middleware\AdminOnly::class, // ✅ make sure this class exists
    ];
}
