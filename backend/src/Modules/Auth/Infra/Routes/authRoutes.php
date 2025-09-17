<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Infra\Controllers\AuthController;

Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});