<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;

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
}
