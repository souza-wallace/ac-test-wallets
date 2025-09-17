<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Wallet\Domain\Services\WalletServiceInterface;

class ReverseTransaction
{
    public function __construct(
        private WalletServiceInterface $walletService
    ) {}

    public function execute(int $transactionId): void
    {
        $this->walletService->reverseTransaction($transactionId);
    }
}