<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\Wallet\Application\UseCases\ReverseTransaction;
use Modules\Wallet\Application\UseCases\Deposit;
use Modules\Wallet\Domain\Repositories\TransactionRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Infra\Persistence\Repositories\UserRepository;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Infra\Persistence\Repositories\WalletRepository;
use Modules\Wallet\Infra\Persistence\Repositories\TransactionRepository;
use Faker\Factory as Faker;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('reverse transaction reverses a transaction by id', function () {
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

    // Criar depósito inicial
    $deposit = app(Deposit::class);
    $amount = 100.0;
    $transaction = $deposit->execute($user->getId(), $amount);

    // Reverter transação por ID
    $reverseTransaction = app(ReverseTransaction::class);
    $reverseTransaction->execute($transaction->getId());

    expect($transaction->getAmount())->toBe($amount)
        ->and($transaction->getUserId())->toBe($user->getId());
});