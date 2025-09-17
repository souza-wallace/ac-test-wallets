<?php

namespace Modules\Auth\Infra\Strategies;

use Modules\Auth\Domain\Strategies\AuthStrategyInterface;
use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

class SessionAuthStrategy implements AuthStrategyInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function generateToken(User $user): string
    {
        return base64_encode(json_encode([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'expires_at' => time() + 3600
        ]));
    }

    public function validateToken(string $token): ?User
    {
        $decoded = json_decode(base64_decode($token), true);
        
        if (!$decoded || $decoded['expires_at'] < time()) {
            return null;
        }
        
        return $this->userRepository->findById($decoded['user_id']);
    }
}