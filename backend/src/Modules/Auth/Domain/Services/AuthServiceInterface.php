<?php

namespace Modules\Auth\Domain\Services;

use Modules\User\Domain\Entities\User;
use Modules\Auth\Domain\Strategies\AuthStrategyInterface;

interface AuthServiceInterface
{
    public function authenticate(string $email, string $password): ?User;
    public function generateToken(User $user): string;
    public function validateToken(string $token): ?User;
    public function setAuthStrategy(AuthStrategyInterface $strategy): void;
}