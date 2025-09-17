<?php

namespace Modules\User\Application\UseCases;

use Modules\User\Domain\Repositories\UserRepositoryInterface;

class GetUserBalance
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $userId): float
    {
        $user = $this->userRepository->findById($userId);
        
        return $user ? $user->getBalance() : 0.0;
    }
}