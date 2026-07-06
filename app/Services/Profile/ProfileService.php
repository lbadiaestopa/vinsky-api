<?php

namespace App\Services\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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
        if ($user->membership()->where('role', 'admin')->exists()) {
            throw new ConflictHttpException(
                'You cannot delete your account while you are an administrator of an orchestra.'
            );
        }
        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update([
                'revoked' => true,
            ]);

        $user->delete();
    }
}
