<?php

namespace Modules\User\Infra\Responses;

use Modules\User\Domain\Entities\User;
use Illuminate\Http\JsonResponse;

class UserResponse
{
    public static function created(User $user): JsonResponse
    {
        return response()->json([
            'message' => 'User created successfully',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'balance' => $user->getBalance()
            ]
        ], 201);
    }

    public static function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'balance' => $user->getBalance()
            ]
        ]);
    }
}