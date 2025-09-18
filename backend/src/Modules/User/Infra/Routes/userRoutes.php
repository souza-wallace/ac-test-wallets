<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Infra\Controllers\UserController;

Route::prefix('api')->middleware('jwt.auth')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{userId}', [UserController::class, 'show']);
    Route::get('/users/{userId}/balance', [UserController::class, 'getBalance']);
});