<?php

namespace Modules\Wallet\Infra\Services;

use Modules\Wallet\Domain\Services\WalletServiceInterface;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\Shared\Exceptions\InsufficientFundsException;
use Illuminate\Support\Facades\DB;

class WalletService implements WalletServiceInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function deposit(int $userId, float $amount): void
    {
        DB::transaction(function () use ($userId, $amount) {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new \InvalidArgumentException('User not found');
            }

            $newBalance = $user->getBalance() + $amount;
            $this->userRepository->updateBalance($userId, $newBalance);

            $transaction = new Transaction(
                null,
                $userId,
                TransactionType::DEPOSIT,
                $amount
            );

            $this->transactionRepository->save($transaction);
        });
    }

    public function transfer(int $fromUserId, int $toUserId, float $amount): void
    {
        DB::transaction(function () use ($fromUserId, $toUserId, $amount) {
            $fromUser = $this->userRepository->findById($fromUserId);
            $toUser = $this->userRepository->findById($toUserId);

            if (!$fromUser || !$toUser) {
                throw new \InvalidArgumentException('User not found');
            }

            if ($fromUser->getBalance() < $amount) {
                throw new InsufficientFundsException();
            }

            $this->userRepository->updateBalance($fromUserId, $fromUser->getBalance() - $amount);
            $this->userRepository->updateBalance($toUserId, $toUser->getBalance() + $amount);

            $transaction = new Transaction(
                null,
                $fromUserId,
                TransactionType::TRANSFER,
                $amount,
                $toUserId
            );

            $this->transactionRepository->save($transaction);
        });
    }

    public function reverseTransaction(int $transactionId): void
    {
        DB::transaction(function () use ($transactionId) {
            $transaction = $this->transactionRepository->findById($transactionId);
            
            if (!$transaction) {
                throw new \InvalidArgumentException('Transaction not found');
            }

            switch ($transaction->getType()) {
                case TransactionType::DEPOSIT:
                    $this->reverseDeposit($transaction);
                    break;
                case TransactionType::TRANSFER:
                    $this->reverseTransfer($transaction);
                    break;
            }
        });
    }

    private function reverseDeposit(Transaction $transaction): void
    {
        $user = $this->userRepository->findById($transaction->getUserId());
        
        if ($user->getBalance() < $transaction->getAmount()) {
            throw new InsufficientFundsException();
        }

        $newBalance = $user->getBalance() - $transaction->getAmount();
        $this->userRepository->updateBalance($transaction->getUserId(), $newBalance);

        $reversalTransaction = new Transaction(
            null,
            $transaction->getUserId(),
            TransactionType::REVERSAL,
            $transaction->getAmount()
        );

        $this->transactionRepository->save($reversalTransaction);
    }

    private function reverseTransfer(Transaction $transaction): void
    {
        $fromUser = $this->userRepository->findById($transaction->getUserId());
        $toUser = $this->userRepository->findById($transaction->getRecipientId());

        if ($toUser->getBalance() < $transaction->getAmount()) {
            throw new InsufficientFundsException();
        }

        $this->userRepository->updateBalance($transaction->getUserId(), $fromUser->getBalance() + $transaction->getAmount());
        $this->userRepository->updateBalance($transaction->getRecipientId(), $toUser->getBalance() - $transaction->getAmount());

        $reversalTransaction = new Transaction(
            null,
            $transaction->getRecipientId(),
            TransactionType::REVERSAL,
            $transaction->getAmount(),
            $transaction->getUserId()
        );

        $this->transactionRepository->save($reversalTransaction);
    }
}