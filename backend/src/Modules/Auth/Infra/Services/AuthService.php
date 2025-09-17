<?php

namespace Modules\Auth\Infra\Services;

use Modules\User\Domain\Entities\User;
use Modules\Auth\Domain\Services\AuthServiceInterface;
use Modules\Auth\Domain\Strategies\AuthStrategyInterface;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

class AuthService implements AuthServiceInterface
{
    private AuthStrategyInterface $authStrategy;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        AuthStrategyInterface $authStrategy
    ) {
        $this->authStrategy = $authStrategy;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !password_verify($password, $user->getPassword())) {
            return null;
        }
        
        return $user;
    }

    public function generateToken(User $user): string
    {
        return $this->authStrategy->generateToken($user);
    }

    public function validateToken(string $token): ?User
    {
        return $this->authStrategy->validateToken($token);
    }

    public function setAuthStrategy(AuthStrategyInterface $strategy): void
    {
        $this->authStrategy = $strategy;
    }
}