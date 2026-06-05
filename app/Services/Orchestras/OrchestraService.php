<?php

namespace App\Services\Orchestras;

use App\Models\Orchestra;
use App\Models\Membership;
use App\Models\User;

class OrchestraService
{
    public function create(User $user, array $data): Orchestra
    {
        $orchestra = Orchestra::create([
            'name' => $data['name'],
            'location' => $data['location'],
        ]);

        Membership::create([
            'user_id' => $user->id,
            'orchestra_id' => $orchestra->id,
            'role' => 'admin',
        ]);

        return $orchestra;
    }
}