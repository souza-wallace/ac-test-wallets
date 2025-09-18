<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Shared\ValueObjects\PaginatedResult;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;

class GetTransactions
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function execute(int $userId)
    {
        return $this->transactionRepository->findByUserId($userId);
    }
}