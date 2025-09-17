<?php

namespace Modules\Wallet\Domain\Repositories;

use Modules\Wallet\Domain\Entities\Wallet;

interface WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet;
    public function save(Wallet $wallet): Wallet;
    public function updateBalance(int $walletId, float $balance): void;
}