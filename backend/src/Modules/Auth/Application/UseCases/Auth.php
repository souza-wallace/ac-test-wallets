<?php

namespace Modules\Auth\Application\UseCases;

use Modules\Auth\Domain\Services\AuthServiceInterface;

class Auth
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function execute(string $email, string $password): ?string
    {
        $user = $this->authService->authenticate($email, $password);
        
        if (!$user) {
            return null;
        }
        
        return $this->authService->generateToken($user);
    }
}