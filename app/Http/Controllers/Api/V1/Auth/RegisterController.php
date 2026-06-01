<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;

class RegisterController extends Controller
{
    public function __construct(
        private RegisterService $registerService,
        private AuthService $authService
    ) {}

    public function store(RegisterRequest $request)
    {
        $user = $this->registerService->register(
            $request->validated()
        );

        $token = $this->authService->issueToken($user);

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }
}
