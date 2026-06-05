<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Services\Profile\ProfileService;
use App\Http\Requests\Api\V1\Profile\ProfileUpdateRequest;
use App\Http\Requests\Api\V1\Profile\UpdatePasswordRequest;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $updatedUser = $this->profileService->update(
            $user,
            $request->validated()
        );

        return new UserResource($updatedUser);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        $this->profileService->updatePassword(
            $user,
            $request->password
        );

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $this->profileService->delete($request->user());

        return response()->noContent();
    }
}
