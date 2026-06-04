<?php

namespace App\Services\Profile;

use App\Models\User;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}