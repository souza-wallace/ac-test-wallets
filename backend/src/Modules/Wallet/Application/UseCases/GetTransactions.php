<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;

class GetTransactions
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function execute(int $userId): array
    {
        return $this->transactionRepository->findByUserId($userId);
    }
}