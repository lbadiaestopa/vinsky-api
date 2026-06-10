<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Membership;
use App\Models\Program;

class EventPolicy
{
    public function create(User $user, Program $program): bool
    {
        return $user->membership()
            ->where('orchestra_id', $program->orchestra_id)
            ->where('role', 'admin')
            ->exists();
    }

    public function viewAll(User $user, Program $program): bool
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $program->orchestra_id)
            ->exists();
    }
}
