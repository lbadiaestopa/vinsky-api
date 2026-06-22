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

/**
* @group Programs
* Endpoints for managing programs. Programs belong to an orchestra and can contain events.
*/
class ProgramController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ProgramService $service
    ) {}

    /**
     * Create a program
     * 
     * User must have a membership linked to the orchestra containing the program with an admin role to create it. 
     */
    public function store(StoreProgramRequest $request, Orchestra $orchestra)
    {
        return $this->service->create($request->validated(), $orchestra);
    }

    /**
     * List all programs
     * 
     * User must have a membership linked to the orchestra containing the programs to see them.
     */
    public function index(Orchestra $orchestra)
    {
        $this->authorize('viewAny', [Program::class, $orchestra]);

        return ProgramResource::collection(
            $this->service->index($orchestra)
        );
    }

    /**
     * Get a program
     * 
     * User must have a membership linked to the orchestra containing the program to see it.
     */
    public function show(Program $program)
    {
        $this->authorize('view', $program);

        return new ProgramResource($program);
    }

    /**
     * Update a program
     * 
     * User must have a membership linked to the orchestra containing the orchestra with an admin role to update it. 
     */
    public function update(UpdateProgramRequest $request, Program $program)
    {
        $program = $this->service->update(
            $program,
            $request->validated(),
        );

        return new ProgramResource($program);
    }

    /**
     * Delete a program
     * 
     * User must have a membership linked to the orchestra containing the orchestra with an admin role to delete it. 
     */
    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);

        $this->service->delete($program);

        return response()->noContent();
    }
}
