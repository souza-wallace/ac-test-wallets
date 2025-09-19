<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\Wallet\Application\UseCases\ReverseTransfer;
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

uses(Tests\TestCase::class, RefreshDatabase::class);

test('reverse transfer reverses a transfer transaction', function () {
    app()->bind(UserRepositoryInterface::class, UserRepository::class);
    app()->bind(WalletRepositoryInterface::class, WalletRepository::class);
    app()->bind(TransactionRepositoryInterface::class, TransactionRepository::class);

    $faker = Faker::create();
    
    // Criar usuário remetente
    $fromUserData = new RegisterUserDto(
        $faker->name(),
        $faker->unique()->safeEmail(),
        $faker->password()
    );
    
    // Criar usuário destinatário
    $toUserData = new RegisterUserDto(
        $faker->name(),
        $faker->unique()->safeEmail(),
        $faker->password()
    );

    $registerUser = app(RegisterUser::class);
    $fromUser = $registerUser->execute($fromUserData);
    $toUser = $registerUser->execute($toUserData);

    // Adicionar saldo ao usuário remetente
    $deposit = app(Deposit::class);
    $deposit->execute($fromUser->getId(), 200.0);

    // Fazer transferência
    $transfer = app(Transfer::class);
    $amount = 50.0;
    $transferTransaction = $transfer->execute($fromUser, $toUser->getEmail(), $amount);

    // Reverter a transferência
    $reverseTransfer = app(ReverseTransfer::class);
    $reverseTransfer->execute($transferTransaction);

    expect($transferTransaction->getAmount())->toBe($amount)
        ->and($transferTransaction->getUserId())->toBe($fromUser->getId());
});