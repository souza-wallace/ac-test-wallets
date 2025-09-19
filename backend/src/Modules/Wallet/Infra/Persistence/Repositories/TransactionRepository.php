<?php

namespace Modules\Wallet\Infra\Persistence\Repositories;

use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Enums\TransactionStatus;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Infra\Persistence\Models\Transaction as TransactionModel;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function save(Transaction $transaction): Transaction
    {
        $transactionModel = $transaction->getId()
            ? TransactionModel::findOrFail($transaction->getId())
            : new TransactionModel();
    
        $transactionModel->fill([
            'user_id' => $transaction->getUserId(),
            'wallet_id' => $transaction->getWalletId(),
            'type' => $transaction->getType()->value,
            'amount' => $transaction->getAmount(),
            'related_wallet' => $transaction->getRecipientWalletId(),
            'status' => $transaction->getStatus(),
            'description' => $transaction->getDescription(),
            'reference_id' => $transaction->getReferenceId(),
            'can_reverse' => $transaction->getCanReverse(),
        ]);
    
        $transactionModel->save();
    
        return $this->toDomainEntity($transactionModel);
    }

    public function findById(int $id): ?Transaction
    {
        $transactionModel = TransactionModel::find($id);
        
        return $transactionModel ? $this->toDomainEntity($transactionModel) : null;
    }

    public function findByUserId(int $userId)
    {
        return TransactionModel::with(['user', 'recipientWallet.user'])->where('user_id', $userId)
        ->orWhere('related_wallet', function($query) use ($userId) {
            $query->select('id')
                ->from('wallets')
                ->where('user_id', $userId);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    }

    private function toDomainEntity(TransactionModel $transactionModel): Transaction
    {

        return Transaction::reconstitute(
            $transactionModel->id,
            $transactionModel->wallet_id,
            $transactionModel->user_id,
            TransactionType::from($transactionModel->type),
            (float)$transactionModel->amount,
            $transactionModel->related_wallet,
            $transactionModel->description,
            $transactionModel->reference_id,
            TransactionStatus::COMPLETED,
            $transactionModel->created_at?->toDateTime()
        );
    }
}