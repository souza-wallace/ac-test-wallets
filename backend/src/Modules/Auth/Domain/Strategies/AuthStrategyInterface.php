<?php

namespace Modules\Auth\Domain\Strategies;

use Modules\User\Domain\Entities\User;

interface AuthStrategyInterface
{
    public function generateToken(User $user): string;
    public function validateToken(string $token): ?User;
}