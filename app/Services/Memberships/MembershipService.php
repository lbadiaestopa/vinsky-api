<?php

namespace App\Services\Memberships;

use App\Models\User;
use App\Models\Orchestra;
use App\Models\Membership;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\Collection;

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

    public function index()
    {
        $user = auth()->user();

        return Membership::query()
            ->where('user_id', $user->id)
            ->with(['user', 'orchestra'])
            ->get();
    }

    public function getByOrchestra(Orchestra $orchestra): Collection
    {
        return $orchestra->memberships()
            ->with('user')
            ->get();
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

    public function update(Membership $membership, array $data): Membership
    {
        $membership->update($data);

        return $membership->fresh([
            'user',
            'orchestra',
        ]);
    }

    public function delete(Membership $membership): void
    {
        $membership->delete();
    }
}
