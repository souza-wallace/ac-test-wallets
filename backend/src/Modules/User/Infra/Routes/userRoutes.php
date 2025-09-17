<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Infra\Controllers\UserController;

Route::prefix('api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{userId}/balance', [UserController::class, 'getBalance']);
});