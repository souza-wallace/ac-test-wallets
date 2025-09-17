<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Wallet\Domain\Services\WalletServiceInterface;

class Transfer
{
    public function __construct(
        private WalletServiceInterface $walletService
    ) {}

    public function execute(int $fromUserId, int $toUserId, float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        if ($fromUserId === $toUserId) {
            throw new \InvalidArgumentException('Cannot transfer to yourself');
        }

        $this->walletService->transfer($fromUserId, $toUserId, $amount);
    }
}