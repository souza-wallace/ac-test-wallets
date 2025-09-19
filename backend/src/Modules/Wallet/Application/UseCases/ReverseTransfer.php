<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Shared\Exceptions\CannotReverseException;
use Modules\Shared\Exceptions\InsufficientBalanceException;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;

class ReverseTransfer
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function execute(Transaction $transaction): void
    {
        $fromWallet = $this->walletRepository->findByUserId($transaction->getUserId());
        $toWallet = $this->walletRepository->findByUserId($transaction->getRecipientWalletId());

        if (!$transaction->getCanReverse()) {
            throw new CannotReverseException();
        }

        if ($toWallet->getBalance() < $transaction->getAmount()) {
            throw new InsufficientBalanceException();
        }

        $this->walletRepository->updateBalance($fromWallet->getId(), $fromWallet->getBalance() + $transaction->getAmount());
        $this->walletRepository->updateBalance($toWallet->getId(), $toWallet->getBalance() - $transaction->getAmount());

        $reversalTransaction = Transaction::create(
            $toWallet->getId(),
            $transaction->getRecipientWalletId(),
            TransactionType::REVERSAL,
            $transaction->getAmount(),
            $transaction->getUserId(),
            'Estorno de transferência',
            $transaction->getId(),
            false
        );

        $this->transactionRepository->save($reversalTransaction);

        $transaction->markAsIrreversible();
        $this->transactionRepository->save($transaction);
    }
}