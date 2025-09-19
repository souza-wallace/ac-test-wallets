<?php

namespace Modules\Wallet\Application\UseCases;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Exceptions\InsufficientBalanceException;
use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Domain\Entities\Transaction;
class Transfer
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository
    ) {}

    public function execute(User $user, String $toUserEmail, float $amount, ?String $description = null): Transaction
    {
        if ($user->getEmail() === $toUserEmail) {
            throw new \InvalidArgumentException('Cannot transfer to yourself');
        }

        return DB::transaction(function () use ($user, $toUserEmail, $amount, $description) {
            $fromUser = $this->userRepository->findByEmailWithWallet($user->getEmail());
            $toUser = $this->userRepository->findByEmailWithWallet($toUserEmail);
        
            if (!$fromUser || !$toUser) {
                throw new \InvalidArgumentException('User not found');
            }
        
            $fromWallet = $fromUser->getWallet();
            $toWallet = $toUser->getWallet();

            if (!$fromWallet || !$toWallet) {
                throw new \InvalidArgumentException('Wallet not found');
            }
        
            if ($fromWallet->getBalance() < $amount) {
                throw new InsufficientBalanceException();
            }
        
            $this->walletRepository->updateBalance(
                $fromWallet->getId(),
                $fromWallet->getBalance() - $amount
            );
        
            $this->walletRepository->updateBalance(
                $toWallet->getId(),
                $toWallet->getBalance() + $amount
            );
        
            $transaction = Transaction::create(
                $fromWallet->getId(),
                $user->getId(),
                TransactionType::TRANSFER,
                $amount,
                $toWallet->getId(),
                $description
            );

            return $this->transactionRepository->save($transaction);
        });
    }
}