<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Infra\Controllers\WalletController;

Route::prefix('api')->middleware('jwt.auth')->group(function () {
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
    Route::post('/wallet/transactions/{transactionId}/reverse', [WalletController::class, 'reverse']);
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactions']);
});