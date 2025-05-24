<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // You can add exceptions here if needed, e.g.
    // protected $except = [
    //     'api/*',
    // ];
}
