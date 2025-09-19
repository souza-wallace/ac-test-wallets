<?php

namespace Modules\Wallet\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Infra\Persistence\Repositories\TransactionRepository;
use Modules\Wallet\Infra\Persistence\Repositories\WalletRepository;

class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        
        // Use Cases
        $this->app->bind(\Modules\Wallet\Application\UseCases\CreateWallet::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Infra/Routes/walletRoutes.php');
    }
}