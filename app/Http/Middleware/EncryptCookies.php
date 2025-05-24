<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    // You can add cookies to the $except array to exclude them from encryption
    // protected $except = [
    //     //
    // ];
}
