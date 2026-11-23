<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use App\Http\Resources\UserResource;

/**
 * @group Authentication
 * Endpoints for user registration, login and logout.
 * Registration and login do not require authentication.
 */
class RegisterController extends Controller
{
    public function __construct(
        private RegisterService $registerService,
    ) {}

    /**
     * Register a new user
     *
     * Creates a new user account and sends an email verification link.
     * The user must verify their email address before logging in.
     *
     * @unauthenticated
     */
    public function store(RegisterRequest $request)
    {
        $user = $this->registerService->register(
            $request->validated()
        );

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registration successful. Please verify your email address.',
            'user' => new UserResource($user),
        ], 201);
    }
}
