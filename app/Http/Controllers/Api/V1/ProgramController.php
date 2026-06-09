<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Program\StoreProgramRequest;
use App\Http\Requests\Api\V1\Program\UpdateProgramRequest;
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
        return $this->service->create($request->validated(), $orchestra);
    }

    public function index(Orchestra $orchestra)
    {
        $this->authorize('viewAny', [Program::class, $orchestra]);

        return ProgramResource::collection(
            $this->service->index($orchestra)
        );
    }

    public function show(Program $program)
    {
        $this->authorize('view', $program);
        
        return new ProgramResource($program);
    }

    public function update(UpdateProgramRequest $request, Program $program)
    {
        $program = $this->service->update(
            $program,
            $request->validated(),
        );

        return new ProgramResource($program);
    }
}
