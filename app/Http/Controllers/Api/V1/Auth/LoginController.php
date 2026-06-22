<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Login
     *
     * @unauthenticated
     * 
     * @group Authentication
     */
    public function store(LoginRequest $request)
    {
        $user = $this->authService->attemptLogin(
            $request->validated()
        );

        if (! $user) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $this->authService->issueToken($user);

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }
}
