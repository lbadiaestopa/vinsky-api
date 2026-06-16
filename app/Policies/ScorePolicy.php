<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\Score;
use App\Models\User;
use App\Models\Membership;

class ScorePolicy
{
    public function create(User $user, Program $program): bool
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $program->orchestra_id)
            ->where('role', 'admin')
            ->exists();
    }

    public function viewAny(User $user, Program $program): bool
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $program->orchestra_id)
            ->exists();
    }

    public function download(User $user, Score $score): bool
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $score->program->orchestra_id)
            ->exists();
    }
}