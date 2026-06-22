<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orchestra\StoreOrchestraRequest;
use App\Http\Requests\Api\V1\Orchestra\UpdateOrchestraRequest;
use App\Http\Resources\OrchestraResource;
use App\Models\Orchestra;
use App\Services\Orchestras\OrchestraService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
* @group Orchestras
* Endpoints for managing orchestras. Orchestras can contain programs.
*/
class OrchestraController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private OrchestraService $service
    ) {}

    /**
     * Create an orchestra
     * 
     * All users can create an orchestra. 
     */
    public function store(StoreOrchestraRequest $request)
    {
        $this->authorize('create', Orchestra::class);

        $orchestra = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return (new OrchestraResource($orchestra))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * List all orchestras
     * 
     * User must have a membership linked to the orchestras to see them. 
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Orchestra::class);

        $orchestras = $this->service->index($request->user());

        return OrchestraResource::collection($orchestras);
    }

    /**
     * Get an orchestra
     * 
     * User must have a membership linked to the orchestra to see it. 
     */
    public function show(Orchestra $orchestra)
    {
        $this->authorize('view', $orchestra);

        return new OrchestraResource($orchestra);
    }

    /**
     * Update an orchestra
     * 
     * User must have a membership linked to the orchestra with an admin role to update it. 
     */
    public function update(UpdateOrchestraRequest $request, Orchestra $orchestra)
    {
        $this->authorize('update', $orchestra);

        $orchestra = $this->service->update(
            $orchestra,
            $request->validated()
        );

        return new OrchestraResource($orchestra);
    }

    /**
     * Delete an orchestra
     * 
     * User must have a membership linked to the orchestra with an admin role to delete it. 
     */
    public function destroy(Orchestra $orchestra)
    {
        $this->authorize('delete', $orchestra);

        $this->service->delete($orchestra);

        return response()->noContent();
    }
}
