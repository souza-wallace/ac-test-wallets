<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('wallet_id')
                ->constrained('wallets')
                ->onDelete('cascade');
        
            $table->foreignId('related_wallet') // destinatário
                ->nullable()
                ->constrained('wallets')
                ->onDelete('cascade');
        
            $table->foreignId('reference_id') // transação de referência
                ->nullable()
                ->constrained('transactions')
                ->onDelete('cascade');
        
            $table->foreignId('user_id') // usuário que fez a transação
                ->constrained('users')
                ->onDelete('cascade');
        
            $table->enum('type', ['DEPOSIT', 'TRANSFER', 'REVERSAL']);
            $table->enum('status', ['PENDING', 'COMPLETED', 'REVERSED'])
                  ->default('PENDING');
        
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
        
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
