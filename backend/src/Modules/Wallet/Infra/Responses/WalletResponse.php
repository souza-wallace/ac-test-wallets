<?php

namespace Modules\Wallet\Infra\Responses;

use Illuminate\Http\JsonResponse;
use Modules\Wallet\Domain\Entities\Transaction;

class WalletResponse
{
    public static function deposit(Transaction $transaction): JsonResponse
    {
        return response()->json([
            'message' => 'Deposit completed with successfull',
            'transaction' => [
                'type' => $transaction->getType(),
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getCreatedAt(),
            ]
        ], 201);
    }

    public static function transfer(Transaction $transaction): JsonResponse
    {
        return response()->json([
            'message' => 'Transfer completed with successfull',
            'transaction' => [
                'from' => $transaction->getId(),
                'to' => $transaction->getRecipientWalletId(),
                'type' => $transaction->getType(),
                'amount' => $transaction->getAmount(),
                'date' => $transaction->getCreatedAt(),
            ]
        ], 201);
    }
}