<?php

namespace App\Policies;

use App\Models\User;
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
}
