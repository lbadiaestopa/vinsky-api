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

class OrchestraController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private OrchestraService $service
    ) {}

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

    public function index(Request $request)
    {
        $this->authorize('viewAny', Orchestra::class);

        $orchestras = $this->service->index($request->user());

        return OrchestraResource::collection($orchestras);
    }

    public function show(Orchestra $orchestra)
    {
        $this->authorize('view', $orchestra);

        return new OrchestraResource($orchestra);
    }

    public function update(UpdateOrchestraRequest $request, Orchestra $orchestra)
    {
        $this->authorize('update', $orchestra);

        $orchestra = $this->service->update(
            $orchestra,
            $request->validated()
        );

        return new OrchestraResource($orchestra);
    }

    public function destroy(Orchestra $orchestra)
    {
        $this->authorize('delete', $orchestra);

        $this->service->delete($orchestra);

        return response()->noContent();
    }
}
