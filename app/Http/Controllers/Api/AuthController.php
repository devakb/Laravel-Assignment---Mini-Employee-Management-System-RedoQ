<?php

namespace App\Http\Controllers\Api;

use App\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\LoginRequest;
use App\Models\User;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse("Invalid credentials", 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
            ]
        ], 'Logged in successfully');
    }
}
