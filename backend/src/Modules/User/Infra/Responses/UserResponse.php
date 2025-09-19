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
                'wallet' => $user->getWallet()
                ? [
                    'id' => $user->getWallet()->getId(),
                    'userId' => $user->getWallet()->getUserId(),
                    'balance' => $user->getWallet()->getBalance(),
                    'createdAt' => $user->getWallet()->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $user->getWallet()->getUpdatedAt()->format('Y-m-d H:i:s'),
                ]
                : null,
            ]
        ]);
    }
}