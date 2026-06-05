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
}
