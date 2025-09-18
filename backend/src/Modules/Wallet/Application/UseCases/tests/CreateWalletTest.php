<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\Wallet\Application\UseCases\CreateWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Infra\Persistence\Repositories\UserRepository;
use Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use Modules\Wallet\Infra\Persistence\Repositories\WalletRepository;
use Faker\Factory as Faker;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('create wallet creates wallet for user', function () {
    app()->bind(UserRepositoryInterface::class, UserRepository::class);
    app()->bind(WalletRepositoryInterface::class, WalletRepository::class);

    $faker = Faker::create();
    
    $data = new RegisterUserDto(
        $faker->name(),
        $faker->unique()->safeEmail(),
        $faker->password()
    );

    $registerUser = app(RegisterUser::class);
    $user = $registerUser->execute($data);

    $createWallet = app(CreateWallet::class);
    $wallet = $createWallet->execute($user->getId());

    expect($wallet->getUserId())->toBe($user->getId())
        ->and($wallet->getBalance())->toBe(0.0);
});