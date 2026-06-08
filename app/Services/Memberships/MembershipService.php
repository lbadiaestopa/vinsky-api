<?php

namespace App\Services\Memberships;

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;
use Illuminate\Http\Exceptions\HttpResponseException;

class MembershipService
{
    public function create(array $data): Membership
    {
        $user = User::where('email', $data['email'])->firstOrFail();

        $orchestra = Orchestra::where('name', $data['orchestra_name'])->firstOrFail();

        $this->ensureNotDuplicate($user->id, $orchestra->id);

        return Membership::create([
            'user_id' => $user->id,
            'orchestra_id' => $orchestra->id,
            'role' => $data['role'] ?? 'member',
            'member_type' => $data['member_type'] ?? null,
            'instrument' => $data['instrument'] ?? null,
            'section' => $data['section'] ?? null,
            'joined_at' => $data['joined_at'] ?? now(),
        ]);
    }

    private function ensureNotDuplicate(int $userId, int $orchestraId): void
    {
        $exists = Membership::where('user_id', $userId)
            ->where('orchestra_id', $orchestraId)
            ->exists();

        if ($exists) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'User already in this orchestra.',
                    'errors' => [
                        'email' => ['User already in this orchestra.'],
                    ],
                ], 409)
            );
        }
    }
}