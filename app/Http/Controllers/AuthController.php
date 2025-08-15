<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponseTrait;
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * register a new user
     */
    public function register(RegisterRequest $request)
    {
        $register = $this->authService->register($request->validated());
        return $this->apiResponse(
            $register,
            201,
            'User registered successfully'
        );
    }
    /**
     * login a user
     */
    public function login(LoginRequest $request)
    {
        $login = $this->authService->login($request->validated());
        if(!$login) return $this->apiResponse([], 401, 'Invalid credentials');

        return $this->apiResponse(
            $login,
            200,
            'User logged in successfully'
        );
    }
    public function logout()
    {
        $this->authService->logoutAll(Auth::user());
        return $this->apiResponse([], 200, 'User logged out successfully');
    }
}
