<?php

namespace Modules\Wallet\Infra\Persistence\Repositories;

use Modules\Wallet\Domain\Entities\Wallet;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Infra\Persistence\Models\Wallet as WalletModel;

class WalletRepository implements WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet
    {
        $walletModel = WalletModel::where('user_id', $userId)->first();
        
        return $walletModel ? $this->toDomainEntity($walletModel) : null;
    }

    public function save(Wallet $wallet): Wallet
    {
        $walletModel = new WalletModel([
            'user_id' => $wallet->getUserId(),
            'balance' => $wallet->getBalance()
        ]);

        $walletModel->save();

        return $this->toDomainEntity($walletModel);
    }

    public function updateBalance(int $walletId, float $balance): void
    {
        WalletModel::where('id', $walletId)->update(['balance' => $balance]);
    }

    private function toDomainEntity(WalletModel $walletModel): Wallet
    {
        return new Wallet(
            $walletModel->id,
            $walletModel->user_id,
            $walletModel->balance,
        );
    }
}