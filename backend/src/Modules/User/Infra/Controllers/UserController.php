<?php

namespace Modules\User\Infra\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Infra\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Modules\User\Application\UseCases\GetUserBalance;
use Modules\User\Application\UseCases\RegisterUser;
use Modules\User\Infra\Responses\UserResponse;
use Modules\Shared\Exceptions\GlobalExceptionHandler;

class UserController extends Controller
{
    public function __construct(
        private GetUserBalance $getUserBalance,
        private RegisterUser $registerUser,

    ) {}

    public function index(Request $request)
    {
        return response()->json('aqui');
    }

    public function store(RegisterUserRequest $request)
    {
        try {
            $data = new RegisterUserDto(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
            );
            
            $user = $this->registerUser->execute($data);
    
            return UserResponse::created($user);

        } catch (\Throwable $exception) {
            return GlobalExceptionHandler::handle($exception, true);
            
        }
    }

    public function getBalance(Request $request, int $userId)
    {
        $balance = $this->getUserBalance->execute($userId);

        return response()->json(['balance' => $balance]);
    }
}