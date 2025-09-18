<?php

namespace Modules\User\Application\UseCases;

use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

class GetUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user): User
    {
        return $this->userRepository->findByIdWithWallet($user->getId());
    }
}