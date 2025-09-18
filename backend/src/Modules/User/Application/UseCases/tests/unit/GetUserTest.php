<?php

use Modules\User\Application\UseCases\RegisterUser;
use Modules\User\Application\UseCases\GetUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Infra\Persistence\Repositories\UserRepository;
use Faker\Factory as Faker;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('getuser returns user', function () {
     app()->bind(UserRepositoryInterface::class, UserRepository::class);
    
     $faker = Faker::create();
 
     // cria usuário fake com dados dinâmicos
     $data = new RegisterUserDto(
         $faker->name(),
         $faker->unique()->safeEmail(),
         $faker->password()
     );
 
     // cria o usuário primeiro
     $registerUser = app(RegisterUser::class);
     $user = $registerUser->execute($data);
 
     // testa o GetUser
     $getUser = app(GetUser::class);
     $foundUser = $getUser->execute($user);

    expect($foundUser->getId())->toBe($user->getId())
        ->and($foundUser->getEmail())->toBe($data->email);
 });