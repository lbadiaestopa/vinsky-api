<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;
use App\Models\Program;

class ProgramPolicy
{
    private function getMembership(User $user, Orchestra $orchestra): ?Membership
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $orchestra->id)
            ->first();
    }

    public function viewAny(User $user, Orchestra $orchestra): bool
    {
        return $this->getMembership($user, $orchestra) !== null;
    }

    public function create(User $user, Orchestra $orchestra): bool
    {
        $membership = $this->getMembership($user, $orchestra);

        return $membership?->role === 'admin';
    }

    public function view(User $user, Program $program): bool
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $program->orchestra_id)
            ->exists();
    }

    public function update(User $user, Program $program): bool
    {
        $membership = $this->getMembership($user, $program->orchestra);

        return $membership?->role === 'admin';
    }

    public function delete(User $user, Program $program): bool
    {
        $membership = $this->getMembership($user, $program->orchestra);

        return $membership?->role === 'admin';
    }
}
