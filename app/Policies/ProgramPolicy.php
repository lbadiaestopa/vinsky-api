<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;

class ProgramPolicy
{
    public function create(User $user, Orchestra $orchestra): bool
    {
        return \App\Models\Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $orchestra->id)
            ->where('role', 'admin')
            ->exists();
    }
}
