<?php

namespace Modules\Auth\Infra\Strategies;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Modules\Auth\Domain\Strategies\AuthStrategyInterface;
use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

class JwtAuthStrategy implements AuthStrategyInterface
{
    private Configuration $config;
    
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
        $key = env('JWT_SECRET', 'default-secret-key');
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($key)
        );
    }

    public function generateToken(User $user): string
    {
        $now = new \DateTimeImmutable();
        
        return $this->config->builder()
            ->issuedBy('wallet-app')
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user_id', $user->getId())
            ->withClaim('email', $user->getEmail())
            ->getToken($this->config->signer(), $this->config->signingKey())
            ->toString();
    }

    public function validateToken(string $token): ?User
    {
        try {
            $parsedToken = $this->config->parser()->parse($token);
            
            $constraints = [
                new IssuedBy('wallet-app'),
                new SignedWith($this->config->signer(), $this->config->signingKey())
            ];
            
            if (!$this->config->validator()->validate($parsedToken, ...$constraints)) {
                return null;
            }
            
            if ($parsedToken->isExpired(new \DateTimeImmutable())) {
                return null;
            }
            
            $userId = $parsedToken->claims()->get('user_id');
            return $this->userRepository->findById($userId);
        } catch (\Exception) {
            return null;
        }
    }
}