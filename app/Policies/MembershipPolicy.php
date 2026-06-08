<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;

class MembershipPolicy
{
    public function create(User $user): bool
    {
        return Membership::where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();
    }
}