<?php

namespace App\Policies;

use App\Models\User;

class OrchestraPolicy
{
    public function create(User $user): bool
    {
        return $user->memberships()
            ->whereNull('orchestra_id')
            ->where('role', 'admin')
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->memberships()
            ->whereNotNull('orchestra_id')
            ->exists();
    }
}
