<?php

namespace Modules\Wallet\Application\UseCases;

use Modules\Wallet\Domain\Entities\Wallet;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;

class CreateWallet
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository
    ) {}

    public function execute(int $userId): Wallet
    {
        $wallet = new Wallet(
            null,
            $userId,
            0.0
        );

        return $this->walletRepository->save($wallet);
    }
}