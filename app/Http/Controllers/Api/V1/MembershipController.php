<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Membership\StoreMembershipRequest;
use App\Http\Requests\Api\V1\Membership\UpdateMembershipRequest;
use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Services\Memberships\MembershipService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Request;

class MembershipController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private MembershipService $service
    ) {}

    public function store(StoreMembershipRequest $request)
    {
        $this->authorize('create', Membership::class);

        $membership = $this->service->create($request->validated());

        return (new MembershipResource($membership))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request)
    {
        $memberships = $this->service->index();

        return MembershipResource::collection($memberships);
    }

    public function update(UpdateMembershipRequest $request, Membership $membership)
    {
        $this->authorize('update', $membership);

        $membership = $this->service->update(
            $membership,
            $request->validated()
        );

        return new MembershipResource($membership);
    }
}
