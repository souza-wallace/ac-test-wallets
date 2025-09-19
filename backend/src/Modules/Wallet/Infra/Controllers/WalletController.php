<?php

namespace Modules\Wallet\Infra\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Shared\Exceptions\GlobalExceptionHandler;
use Modules\Wallet\Application\UseCases\Deposit;
use Modules\Wallet\Application\UseCases\Transfer;
use Modules\Wallet\Application\UseCases\ReverseTransaction;
use Modules\Wallet\Application\UseCases\GetTransactions;
use Modules\Wallet\Infra\Requests\TransferRequest;
use Modules\Wallet\Infra\Responses\TransactionResponse;
use Modules\Wallet\Infra\Responses\WalletResponse;

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
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01'
            ]);

            $userId = $request->attributes->get('user')->getId();
            $transaction = $this->deposit->execute($userId, $request->amount);
            return WalletResponse::deposit($transaction);
        } catch (\Throwable $exception) {
            return GlobalExceptionHandler::handle($exception, true);
        }
    }

    public function transfer(TransferRequest $request)
    {
        try {
            $user = $request->attributes->get('user');

            $transaction = $this->transfer->execute($user, $request->email, $request->amount, $request->description);
            
            return WalletResponse::transfer($transaction);

            return response()->json(['message' => 'Transfer successful']);
        } catch (\Throwable $exception) {
            return GlobalExceptionHandler::handle($exception, true);
        }
    }

    public function reverse(Request $request, int $transactionId)
    {
        try {
            $this->reverseTransaction->execute($transactionId);

            return response()->json(['message' => 'Transaction reversed successfully']);
            
        } catch (\Throwable $exception) {
            return GlobalExceptionHandler::handle($exception, true);
        }
    }

    public function getTransactions(Request $request)
    {
        try {
            $userId = $request->attributes->get('user')->getId();

            $transactions = $this->getTransactions->execute($userId);
        
            return response()->json(TransactionResponse::collection($transactions));
            
        } catch (\Throwable $exception) {
            return GlobalExceptionHandler::handle($exception, true);
        }
    }
}