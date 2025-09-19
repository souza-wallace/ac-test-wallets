<?php

namespace Modules\User\Infra\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\Application\DTOs\RegisterUserDto;
use Modules\User\Infra\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Modules\User\Application\UseCases\RegisterUser;
use Modules\User\Infra\Responses\UserResponse;
use Modules\Shared\Exceptions\GlobalExceptionHandler;
use Modules\User\Application\UseCases\GetUser;
use Modules\User\Domain\Entities\User;

class UserController extends Controller
{
    public function __construct(
        private RegisterUser $registerUser,
        private GetUser $getUser
    ) {}

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

    public function show(Request $request, Int $id){
        try {
            $user = $request->attributes->get('user');

            $user = $this->getUser->execute($user);

            return UserResponse::show($user);

        } catch (\Throwable $exception) {
            return GlobalExceptionHandler::handle($exception, true);

        }
    }
}