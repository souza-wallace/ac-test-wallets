<?php

namespace Modules\User\Application\UseCases;

use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

class RegisterUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(RegisterUserDto $data): User
    {
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        
        $user = new User(null, $data->name, $data->email, $hashedPassword);

        return $this->userRepository->save($user);
    }
}