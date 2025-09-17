<?php

namespace Modules\Wallet\Infra\Persistence\Repositories;

use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Domain\Enums\TransactionType;
use Modules\Wallet\Infra\Persistence\Models\Transaction as TransactionModel;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function save(Transaction $transaction): Transaction
    {
        $transactionModel = new TransactionModel([
            'user_id' => $transaction->getUserId(),
            'type' => $transaction->getType()->value,
            'amount' => $transaction->getAmount(),
            'recipient_id' => $transaction->getRecipientId()
        ]);
        
        $transactionModel->save();
        
        return $this->toDomainEntity($transactionModel);
    }

    public function findById(int $id): ?Transaction
    {
        $transactionModel = TransactionModel::find($id);
        
        return $transactionModel ? $this->toDomainEntity($transactionModel) : null;
    }

    public function findByUserId(int $userId): array
    {
        $transactionModels = TransactionModel::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return $transactionModels->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    private function toDomainEntity(TransactionModel $transactionModel): Transaction
    {
        return new Transaction(
            $transactionModel->id,
            $transactionModel->user_id,
            TransactionType::from($transactionModel->type),
            $transactionModel->amount,
            $transactionModel->recipient_id,
            $transactionModel->created_at
        );
    }
}