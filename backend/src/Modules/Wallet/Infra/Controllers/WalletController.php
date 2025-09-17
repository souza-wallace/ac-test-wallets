<?php

namespace Modules\Wallet\Infra\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Wallet\Application\UseCases\Deposit;
use Modules\Wallet\Application\UseCases\Transfer;
use Modules\Wallet\Application\UseCases\ReverseTransaction;
use Modules\Wallet\Application\UseCases\GetTransactions;

class WalletController extends Controller
{
    public function __construct(
        private Deposit $deposit,
        private Transfer $transfer,
        private ReverseTransaction $reverseTransaction,
        private GetTransactions $getTransactions
    ) {}

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $userId = $request->get('auth_user')->getId();
            $this->deposit->execute($userId, $request->amount);
            return response()->json(['message' => 'Deposit successful']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $fromUserId = $request->get('auth_user')->getId();
            $this->transfer->execute($fromUserId, $request->to_user_id, $request->amount);
            return response()->json(['message' => 'Transfer successful']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function reverse(Request $request, int $transactionId)
    {
        try {
            $this->reverseTransaction->execute($transactionId);
            return response()->json(['message' => 'Transaction reversed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getTransactions(Request $request)
    {
        $userId = $request->get('auth_user')->getId();
        $transactions = $this->getTransactions->execute($userId);
        return response()->json(['transactions' => $transactions]);
    }
}