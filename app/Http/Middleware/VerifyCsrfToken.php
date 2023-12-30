<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'Stocks/dp/save',
        'Stocks/dp/read-sn-file',
        'Issuance/*',
        'Stocks/delivery/upload_document'
    ];
}
