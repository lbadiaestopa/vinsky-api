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

    public function viewAny(User $user): bool
    {
        return $user->membership()->exists();
    }

    public function view(User $user, Membership $membership): bool
    {
        return $user->membership()
            ->where('orchestra_id', $membership->orchestra_id)
            ->exists();
    }

    public function update(User $user, Membership $membership): bool
    {
        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $membership->orchestra_id)
            ->where('role', 'admin')
            ->exists();
    }

    public function delete(User $user, Membership $membership): bool
    {
        if ($user->id === $membership->user_id) {
            return false;
        }

        return Membership::query()
            ->where('user_id', $user->id)
            ->where('orchestra_id', $membership->orchestra_id)
            ->where('role', 'admin')
            ->exists();
    }
}
