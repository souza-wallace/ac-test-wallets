<?php

namespace Modules\Wallet\Infra\Responses;

use Illuminate\Http\JsonResponse;
use Modules\Wallet\Domain\Entities\Transaction;

class TransactionResponse
{
    public static function fromDomain(Transaction $transaction): array
    {
        return [
            'id' => $transaction->getId(),
            'walletId' => $transaction->getWalletId(),
            'userId' => $transaction->getUserId(),
            'type' => $transaction->getType()->value,
            'amount' => $transaction->getAmount(),
            'recipientWalletId' => $transaction->getRecipientWalletId(),
            'description' => $transaction->getDescription(),
            'referenceId' => $transaction->getReferenceId(),
            'status' => $transaction->getStatus()->value,
            'createdAt' => $transaction->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public static function collection($transactions): array
    {
        return [
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ];
    }
}
