<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Infra\Controllers\UserController;

Route::prefix('api')->middleware('jwt.auth')->group(function () {
    Route::post('/users', [UserController::class, 'store'])->withoutMiddleware('jwt.auth');
    Route::get('/users/{userId}', [UserController::class, 'show']);
});