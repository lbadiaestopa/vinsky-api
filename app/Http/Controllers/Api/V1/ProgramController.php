<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Program\StoreProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Orchestra;
use App\Models\Program;
use App\Services\Programs\ProgramService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProgramController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ProgramService $service
    ) {}

    public function store(StoreProgramRequest $request, Orchestra $orchestra)
    {
        $this->authorize('create', [Program::class, $orchestra]);

        return $this->service->create($request->validated(), $orchestra);
    }

    public function index(Orchestra $orchestra)
    {
        $this->authorize('viewAny', [Program::class, $orchestra]);

        return ProgramResource::collection(
            $this->service->index($orchestra)
        );
    }
}
