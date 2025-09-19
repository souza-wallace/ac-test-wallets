<?php

namespace Modules\Wallet\Domain\Repositories;

use Modules\Wallet\Domain\Entities\Transaction;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction): Transaction;
    public function findById(int $id): ?Transaction;
    public function findByUserId(int $userId);
}