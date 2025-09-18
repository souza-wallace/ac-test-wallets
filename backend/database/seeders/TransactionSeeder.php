<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Wallet\Infra\Persistence\Models\Transaction as ModelsTransaction;
use Modules\Wallet\Infra\Persistence\Models\Wallet as ModelsWallet;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transactions')->truncate();

        /**
         * 1. Alice (user_id = 1) faz depósito de 100 na wallet 1
         */
        $deposit = ModelsTransaction::create([
            'user_id' => 1,
            'wallet_id' => 1,
            'type' => 'DEPOSIT',
            'amount' => 100,
            'status' => 'COMPLETED',
            'description' => 'Depósito inicial',
        ]);
        ModelsWallet::where('id', 1)->increment('balance', 100);

        /**
         * 2. Alice transfere 50 para Bob (wallet 2, user_id 2)
         */
        $transferDebit = ModelsTransaction::create([
            'user_id' => 1,
            'wallet_id' => 1,
            'related_wallet' => 2,
            'type' => 'TRANSFER',
            'amount' => -50,
            'status' => 'COMPLETED',
            'description' => 'Transferência para Bob',
        ]);

        $transferCredit = ModelsTransaction::create([
            'user_id' => 2,
            'wallet_id' => 2,
            'related_wallet' => 1,
            'type' => 'TRANSFER',
            'amount' => 50,
            'status' => 'COMPLETED',
            'description' => 'Transferência recebida de Alice',
        ]);

        ModelsWallet::where('id', 1)->decrement('balance', 50);
        ModelsWallet::where('id', 2)->increment('balance', 50);

        /**
         * 3. Charlie (user_id = 3) faz depósito de 200 na wallet 3
         */
        $depositCharlie = ModelsTransaction::create([
            'user_id' => 3,
            'wallet_id' => 3,
            'type' => 'DEPOSIT',
            'amount' => 200,
            'status' => 'COMPLETED',
            'description' => 'Depósito inicial de Charlie',
        ]);
        ModelsWallet::where('id', 3)->increment('balance', 200);

        /**
         * 4. Dave (user_id = 4) tenta transferir 30 para Alice (wallet 1) sem saldo
         */
        $transferDave = ModelsTransaction::create([
            'user_id' => 4,
            'wallet_id' => 4,
            'related_wallet' => 1,
            'type' => 'TRANSFER',
            'amount' => -30,
            'status' => 'COMPLETED',
            'description' => 'Transferência sem saldo',
        ]);
        ModelsWallet::where('id', 4)->decrement('balance', 30);
        ModelsWallet::where('id', 1)->increment('balance', 30);

        /**
         * 5. Reversão da transferência de Dave
         */
        $reversal = ModelsTransaction::create([
            'user_id' => 4,
            'wallet_id' => 4,
            'type' => 'REVERSAL',
            'amount' => 30,
            'status' => 'COMPLETED',
            'reference_id' => $transferDave->id,
            'description' => 'Reversão da transferência sem saldo',
        ]);

        ModelsWallet::where('id', 4)->increment('balance', 30); // devolve o saldo do Dave
        ModelsWallet::where('id', 1)->decrement('balance', 30); // desfaz crédito em Alice
    }
}
