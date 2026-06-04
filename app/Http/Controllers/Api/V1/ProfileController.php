<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Services\Profile\ProfileService;
use App\Http\Requests\Api\V1\Profile\ProfileUpdateRequest;

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
}
