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

/**
* @group Memberships
* Endpoints for managing orchestras. Orchestras can contain programs.
*/
class MembershipController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private MembershipService $service
    ) {}

    /**
     * Create a membership
     * 
     * User must have a membership linked to the orchestras in order to create another membership. 
     */
    public function store(StoreMembershipRequest $request)
    {
        $this->authorize('create', Membership::class);

        $membership = $this->service->create($request->validated());

        return (new MembershipResource($membership))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * List all memberships
     * 
     * Lists all mermberships of an orcchestra.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Membership::class);

        $memberships = $this->service->index();

        return MembershipResource::collection($memberships);
    }

    /**
     * Get a membership
     * 
     * Lists one mermberships of an orcchestra.
     */
    public function show(Membership $membership)
    {
        $this->authorize('view', $membership);

        return new MembershipResource($membership);
    }

    /**
     * Update membership
     * 
     * User must have a membership linked to the an orchestra with a role of admin in order to update the membership. 
     */
    public function update(UpdateMembershipRequest $request, Membership $membership)
    {
        $this->authorize('update', $membership);

        $membership = $this->service->update(
            $membership,
            $request->validated()
        );

        return new MembershipResource($membership);
    }

    /**
     * Delete membership
     * 
     * User must have a membership linked to the an orchestra with a role of admin in order to delete the membership. 
     */
    public function destroy(Membership $membership)
    {
        $this->authorize('delete', $membership);

        $this->service->delete($membership);

        return response()->noContent();
    }
}
