<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Membership;
use App\Models\Orchestra;

class OrchestraPolicy
{
    public function create(User $user): bool
    {
        return $user->membership()
            ->whereNull('orchestra_id')
            ->where('role', 'admin')
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->membership()
            ->whereNotNull('orchestra_id')
            ->exists();
    }

    public function view(User $user, Orchestra $orchestra): bool
    {
        return Membership::where('user_id', $user->id)
            ->where('orchestra_id', $orchestra->id)
            ->exists();
    }

    public function update(User $user, Orchestra $orchestra): bool
    {
        return Membership::where('user_id', $user->id)
            ->where('orchestra_id', $orchestra->id)
            ->where('role', 'admin')
            ->exists();
    }

    public function delete(User $user, Orchestra $orchestra): bool
    {
        return Membership::where('user_id', $user->id)
            ->where('orchestra_id', $orchestra->id)
            ->where('role', 'admin')
            ->exists();
    }
}
