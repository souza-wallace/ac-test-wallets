<?php

namespace Modules\Auth\Infra\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Domain\Services\AuthServiceInterface;

class JwtAuthMiddleware
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        $user = $this->authService->validateToken($token);

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}