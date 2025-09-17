<?php

namespace Modules\Wallet\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Services\WalletServiceInterface;
use Modules\Wallet\Infra\Persistence\Repositories\TransactionRepository;
use Modules\Wallet\Infra\Services\WalletService;

class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(WalletServiceInterface::class, WalletService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Infra/Routes/walletRoutes.php');
    }
}