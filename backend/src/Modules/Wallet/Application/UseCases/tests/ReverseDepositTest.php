<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\Wallet\Application\UseCases\ReverseDeposit;
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
use Modules\Shared\Exceptions\InsufficientBalanceException;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('reverse deposit reverses a deposit transaction', function () {
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
    $depositTransaction = $deposit->execute($user->getId(), $amount);

    // Reverter o depósito
    $reverseDeposit = app(ReverseDeposit::class);
    $reverseDeposit->execute($depositTransaction);

    expect($depositTransaction->getAmount())->toBe($amount)
        ->and($depositTransaction->getUserId())->toBe($user->getId());
});

test('reverse deposit should throw exception InsufficientBalanceException', function () {
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
    $depositTransaction = $deposit->execute($user->getId(), $amount);

    $depositTransaction->adjustAmount(200);
    
    // Reverter o depósito
    $reverseDeposit = app(ReverseDeposit::class);

    expect(fn() => $reverseDeposit->execute($depositTransaction))
    ->toThrow(InsufficientBalanceException::class);
});