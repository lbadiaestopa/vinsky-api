<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Orchestra\StoreOrchestraRequest;
use App\Http\Resources\OrchestraResource;
use App\Models\Orchestra;
use App\Services\Orchestras\OrchestraService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
}