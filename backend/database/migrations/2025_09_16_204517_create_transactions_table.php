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

            $table->foreignId('related_wallet')
                ->nullable()
                ->constrained('wallets')
                ->onDelete('cascade');

            $table->enum('type', ['DEPOSIT', 'TRANSFER', 'REVERSAL']);
            $table->decimal('amount', 15, 2);
            
            $table->enum('status', ['PENDING', 'COMPLETED', 'REVERSED'])
                  ->default('PENDING');

            $table->foreignId('reference_id')
                ->nullable()
                ->constrained('transactions')
                ->onDelete('cascade');

            $table->string('description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
