<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('store', [UserController::class, 'store']);
Route::put('update/profile', [UserController::class, 'update']);