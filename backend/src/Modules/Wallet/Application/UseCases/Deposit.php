<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Wallet\Domain\Services\WalletServiceInterface;

class Deposit
{
    public function __construct(
        private WalletServiceInterface $walletService
    ) {}

    public function execute(int $userId, float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        $this->walletService->deposit($userId, $amount);
    }
}