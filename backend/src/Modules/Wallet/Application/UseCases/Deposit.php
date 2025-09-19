<?php

namespace Modules\Wallet\Application\UseCases;

use Illuminate\Support\Facades\DB;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Entities\Wallet;

class Deposit
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function execute(int $userId, float $amount): Transaction
    {
        return DB::transaction(function () use ($userId, $amount) {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new \InvalidArgumentException('User not found');
            }

            $wallet = $this->walletRepository->findByUserId($userId);

            $newBalance = $wallet->getBalance() + $amount;
            $this->walletRepository->updateBalance($wallet->getId(), $newBalance);

            $transaction = Transaction::create(
                $userId,
                $wallet->getId(),
                TransactionType::DEPOSIT,
                $amount
            );

            return $this->transactionRepository->save($transaction);
        });
    }
}