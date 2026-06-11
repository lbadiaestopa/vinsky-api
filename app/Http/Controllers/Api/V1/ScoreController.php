<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Score\StoreScoreRequest;
use App\Http\Resources\ScoreResource;
use App\Models\Program;
use App\Models\Score;
use App\Services\Scores\ScoreService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ScoreController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ScoreService $service
    ) {}

    public function store(Program $program, StoreScoreRequest $request)
    {
        $this->authorize('create', [Score::class, $program]);

        $score = $this->service->create(
            $request->validated(),
            $program
        );

        return response()->json([
            'data' => new ScoreResource($score),
        ], 201);
    }

    public function index(Program $program)
    {
        $scores = $this->service->getScoresByProgram($program);

        return response()->json([
            'data' => ScoreResource::collection($scores),
        ]);
    }
}
