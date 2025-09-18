<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\Wallet\Application\UseCases\GetTransactions;
use Modules\Wallet\Application\UseCases\Deposit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Infra\Persistence\Repositories\UserRepository;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Infra\Persistence\Repositories\WalletRepository;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Modules\Wallet\Infra\Persistence\Repositories\TransactionRepository;
use Faker\Factory as Faker;
use Modules\Wallet\Domain\Entities\Transaction;
use Modules\Wallet\Domain\Enums\TransactionStatus;
use Modules\Wallet\Domain\Enums\TransactionType;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('get transactions returns user transactions', function () {
    app()->bind(UserRepositoryInterface::class, UserRepository::class);
    app()->bind(WalletRepositoryInterface::class, WalletRepository::class);
    app()->bind(TransactionRepositoryInterface::class, TransactionRepository::class);

    $faker = Faker::create();
    
    $data = new RegisterUserDto(
        $faker->name(),
        $faker->unique()->safeEmail(),
        $faker->password()
    );

    $registerUser = app(RegisterUser::class);
    $user = $registerUser->execute($data);

    // Criar algumas transações
    $deposit = app(Deposit::class);
    $deposit->execute($user->getId(), 100.0);
    $deposit->execute($user->getId(), 50.0);

    $getTransactions = app(GetTransactions::class);
    $transactions = $getTransactions->execute($user->getId());
    $transaction = Transaction::reconstitute(
        $transactions[0]['id'],
        $transactions[0]['wallet_id'],
        $transactions[0]['user_id'],
        TransactionType::from($transactions[0]['type']),
        (float) $transactions[0]['amount'],
        $transactions[0]['recipient_wallet_id'] ?? null,
        $transactions[0]['description'] ?? null,
        $transactions[0]['reference_id'] ?? null,
        TransactionStatus::from($transactions[0]['status']),
        $transactions[0]['created_at']
    );
    
    expect(count($transactions))->toBe(2)
        ->and($transaction->getUserId())->toBe($user->getId());
});