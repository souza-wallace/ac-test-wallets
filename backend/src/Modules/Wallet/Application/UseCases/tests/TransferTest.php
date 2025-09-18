<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\Wallet\Application\UseCases\Transfer;
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

//Test para transacao de sucesso
test('transfer moves amount between wallets', function () {
    [$fromUser, $toUser] = createUsersWithBalance(200.0);

    $transfer = app(Transfer::class);
    $amount = 50.0;
    $transaction = $transfer->execute($fromUser->getId(), $toUser->getId(), $amount);

    expect($transaction->getAmount())->toBe($amount)
        ->and($transaction->getUserId())->toBe($fromUser->getId());
});

// Teste para saldo insuficiente
test('Transfer should throw InsufficientBalanceException when balance is insufficient', function () {
    [$fromUser, $toUser] = createUsersWithBalance(200.0);
    
    $transfer = app(Transfer::class);
    
    expect(fn() => $transfer->execute($fromUser->getId(), $toUser->getId(), 250.0))
        ->toThrow(InsufficientBalanceException::class);
});

// Teste para usuário não encontrado  
test('Transfer should throw InvalidArgumentException for non-existent user', function () {
    [$fromUser] = createUsersWithBalance(200.0);
    
    $transfer = app(Transfer::class);
    
    expect(fn() => $transfer->execute($fromUser->getId(), 100000, 250.0))
        ->toThrow(InvalidArgumentException::class, 'User not found');
});

// Teste para transferência para si mesmo
test('Transfer should throw InvalidArgumentException when transferring to self', function () {
    [$fromUser] = createUsersWithBalance(200.0);
    
    $transfer = app(Transfer::class);
    
    expect(fn() => $transfer->execute($fromUser->getId(), $fromUser->getId(), 250.0))
        ->toThrow(InvalidArgumentException::class, 'Cannot transfer to yourself');
});

function createUsersWithBalance(float $balance): array
{
    app()->bind(UserRepositoryInterface::class, UserRepository::class);
    app()->bind(WalletRepositoryInterface::class, WalletRepository::class);
    app()->bind(TransactionRepositoryInterface::class, TransactionRepository::class);

    $faker = Faker::create();
    
    $fromUserData = new RegisterUserDto(
        $faker->name(),
        $faker->unique()->safeEmail(),
        $faker->password()
    );
    
    $toUserData = new RegisterUserDto(
        $faker->name(),
        $faker->unique()->safeEmail(),
        $faker->password()
    );

    $registerUser = app(RegisterUser::class);
    $fromUser = $registerUser->execute($fromUserData);
    $toUser = $registerUser->execute($toUserData);

    $deposit = app(Deposit::class);
    $deposit->execute($fromUser->getId(), $balance);

    return [$fromUser, $toUser];
}