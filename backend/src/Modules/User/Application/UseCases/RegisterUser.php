<?php

namespace Modules\User\Application\UseCases;

use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\Wallet\Application\UseCases\CreateWallet;

class RegisterUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CreateWallet $createWallet
    ) {}

    public function execute(RegisterUserDto $data): User
    {
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        
        $user = new User(null, $data->name, $data->email, $hashedPassword);

        $savedUser = $this->userRepository->save($user);
        
        // cria carteira para o usuário
        $this->createWallet->execute($savedUser->getId());
        
        return $savedUser;
    }
}