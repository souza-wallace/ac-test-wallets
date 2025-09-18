<?php

namespace Modules\Wallet\Application\UseCases;

use Illuminate\Support\Facades\DB;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;

class ReverseTransaction
{
    public function __construct(
        private ReverseDeposit $reverseDeposit,
        private ReverseTransfer $reverseTransfer,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function execute(int $transactionId): void
    {
        DB::transaction(function () use ($transactionId) {
            $transaction = $this->transactionRepository->findById($transactionId);

            if (!$transaction) {
                throw new \InvalidArgumentException('Transaction not found');
            }

            match ($transaction->getType()) {
                TransactionType::DEPOSIT => $this->reverseDeposit->execute($transaction),
                TransactionType::TRANSFER => $this->reverseTransfer->execute($transaction),
                default => throw new \DomainException('Unsupported transaction type'),
            };
        });
    }
}
