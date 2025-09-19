<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Modules\Wallet\Infra\Persistence\Models\Wallet as ModelsWallet;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        ModelsWallet::query()->delete();

        ModelsWallet::insert([
            ['id' => 1, 'user_id' => 1, 'balance' => 0],     // Alice (sem saldo inicial)
            ['id' => 2, 'user_id' => 2, 'balance' => 0],     // Bob
            ['id' => 3, 'user_id' => 3, 'balance' => 0],     // Charlie
            ['id' => 4, 'user_id' => 4, 'balance' => 0],     // Dave
        ]);
    }
}
