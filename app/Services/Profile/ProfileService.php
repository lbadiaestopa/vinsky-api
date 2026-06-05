<?php

namespace App\Services\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function updatePassword(User $user, string $password): User
    {
        $user->update([
            'password' => Hash::make($password),
        ]);

        return $user;
    }

    public function delete(User $user): void
    {
        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update([
                'revoked' => true,
            ]);

        $user->delete();
    }
}
