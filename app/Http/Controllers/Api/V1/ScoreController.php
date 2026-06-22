<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Score\StoreScoreRequest;
use App\Http\Resources\ScoreResource;
use App\Models\Program;
use App\Models\Score;
use App\Services\Scores\ScoreService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
* @group Scores
* Endpoints for managing scores. Scores belong to a program.
*/
class ScoreController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ScoreService $service
    ) {}

    /**
     * Create a score
     * 
     * User must have a membership linked to the orchestra containing the score with an admin role to create it. 
     */
    public function store(Program $program, StoreScoreRequest $request)
    {
        $this->authorize('create', [Score::class, $program]);

        $score = $this->service->create(
            array_merge($request->validated(), [
                'sanitized_original_name' => $request->input('sanitized_original_name'),
            ]),
            $program
        );

        return response()->json([
            'data' => new ScoreResource($score),
        ], 201);
    }

    /**
     * List all scores
     * 
     * User must have a membership linked to the orchestra containing the score to see them. 
     */
    public function index(Program $program)
    {
        $this->authorize('viewAny', [Score::class, $program]);

        $scores = $this->service->getScoresByProgram($program);

        return response()->json([
            'data' => ScoreResource::collection($scores),
        ]);
    }

    /**
     * Download score
     * 
     * User must have a membership linked to the orchestra containing the score to download it. 
     */
    public function download(Program $program, Score $score)
    {
        $this->authorize('download', [$score]);

        return $this->service->downloadScore($score);
    }

    /**
     * Delete a score
     * 
     * User must have a membership linked to the orchestra containing the score with an admin role to delete it. 
     */
    public function destroy(Program $program, Score $score)
    {
        $this->authorize('delete', [$program]);

        $this->service->delete($score);

        return response()->noContent();
    }
}
