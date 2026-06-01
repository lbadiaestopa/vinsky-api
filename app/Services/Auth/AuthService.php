<?php

namespace App\Services\Auth;

use App\Models\User;

class AuthService
{
    public function issueToken(User $user): string
    {
        return $user->createToken('auth_token')->accessToken;
    }
}
