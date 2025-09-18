<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Shared\Exceptions\InsufficientBalanceException;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;

class ReverseDeposit
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function execute(Transaction $transaction): void
    {
        $wallet = $this->walletRepository->findByUserId($transaction->getUserId());
        
        if ($wallet->getBalance() < $transaction->getAmount()) {
            throw new InsufficientBalanceException();
        }

        $newBalance = $wallet->getBalance() - $transaction->getAmount();
        $this->walletRepository->updateBalance($wallet->getId(), $newBalance);

        $reversalTransaction = Transaction::createNew(
            $transaction->getUserId(),
            $wallet->getId(),
            TransactionType::REVERSAL,
            $transaction->getAmount()
        );

        $this->transactionRepository->save($reversalTransaction);
    }
}