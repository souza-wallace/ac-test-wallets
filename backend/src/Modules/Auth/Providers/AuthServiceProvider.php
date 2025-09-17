<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Domain\Services\AuthServiceInterface;
use Modules\Auth\Domain\Strategies\AuthStrategyInterface;
use Modules\Auth\Infra\Services\AuthService;
use Modules\Auth\Infra\Strategies\JwtAuthStrategy;
use Modules\Auth\Infra\Strategies\SessionAuthStrategy;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthStrategyInterface::class, function ($app) {
            $strategy = config('auth.strategy', 'jwt');
            $userRepository = $app->make(UserRepositoryInterface::class);
            
            return match ($strategy) {
                'jwt' => new JwtAuthStrategy($userRepository),
                'session' => new SessionAuthStrategy($userRepository),
                default => new JwtAuthStrategy($userRepository)
            };
        });

        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Infra/Routes/authRoutes.php');
    }
}