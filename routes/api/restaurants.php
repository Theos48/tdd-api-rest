<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/store', [RestaurantController::class, 'store']);
    Route::put('{restaurant}/edit', [RestaurantController::class, 'update']);
    Route::apiResource('menu', RestaurantController::class);
});
