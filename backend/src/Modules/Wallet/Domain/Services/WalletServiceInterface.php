<?php

namespace Modules\Wallet\Domain\Services;

interface WalletServiceInterface
{
    public function deposit(int $userId, float $amount): void;
    public function transfer(int $fromUserId, int $toUserId, float $amount): void;
    public function reverseTransaction(int $transactionId): void;
}