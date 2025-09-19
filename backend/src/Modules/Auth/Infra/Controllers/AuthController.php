<?php

namespace Modules\Auth\Infra\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Application\UseCases\Auth;

class AuthController extends Controller
{
    public function __construct(
        private Auth $auth
    ) {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $token = $this->auth->execute(
            $request->email,
            $request->password
        );

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['data' => $token]);
    }
}