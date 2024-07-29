<?php

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function() {
            $PREFIX_API = 'api/v1';
            $PREFIX_API_APP_AUTH = $PREFIX_API . '/auth';
            $PREFIX_API_APP_USER = $PREFIX_API . '/users';

            Route::middleware('api')
                -> prefix($PREFIX_API)
                -> group(base_path('routes/api.php'));

            Route::middleware('api')
                -> prefix($PREFIX_API_APP_AUTH)
                -> group(base_path('routes/api/auth.php'));

            Route::middleware('api')
                -> prefix($PREFIX_API_APP_USER)
                -> group(base_path('routes/api/users.php'));
        },
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e) {
            return ApiResponseHelper::errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, $e->errors());
        });
    })->create();
