<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    use HttpResponses;

    public function login(LoginRequest $request) {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->error(null, 'Email or password is incorrect!', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookie = cookie('token', $token, 60 * 24);

        return $this->success([
            'user' => $user,
        ], 'User logged in successfully')->withCookie($cookie);
    }

    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'role' => $request->role,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'number_phone' => $data['number_phone'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookie = cookie('token', $token, 60 * 24);

        return $this->success([
            'user' => $user,
        ], 'User registered successfully')->withCookie($cookie);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        $cookie = cookie()->forget('token');

        return $this->success(null, 'Logged out successfully!')->withCookie($cookie);
    }

    public function user(Request $request) {
        return $this->success($request->user(), 'Data retrieved successfully!', 200);
    }
}
