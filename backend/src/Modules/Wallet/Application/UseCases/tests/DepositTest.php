<?php

use Modules\User\Application\UseCases\RegisterUser;
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
use Modules\Shared\Exceptions\UserNotfoundException;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('deposit adds amount to wallet balance', function () {
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

    $deposit = app(Deposit::class);
    $amount = 100.50;
    $transaction = $deposit->execute($user->getId(), $amount);

    expect($transaction->getAmount())->toBe($amount)
        ->and($transaction->getUserId())->toBe($user->getId());
});


// Teste para usuário não encontrado
test('deposit must return userNotFoundException', function () {
    $amount = 100.50;
    
    $deposit = app(Deposit::class);

    expect(fn() => $deposit->execute(1000, $amount))
    ->toThrow(UserNotfoundException::class);
});  