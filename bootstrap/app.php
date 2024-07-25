<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function() {
            $PREFIX_API = 'api';
            $PREFIX_API_APP = $PREFIX_API . '/v1';
            $PREFIX_API_APP_AUTH = $PREFIX_API . '/auth';

            Route::middleware('api')
                -> prefix($PREFIX_API)
                -> group(base_path('routes/api.php'));

            Route::middleware('api')
                -> prefix($PREFIX_API_APP_AUTH)
                -> group(base_path('routes/api/auth.php'));
        },
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
