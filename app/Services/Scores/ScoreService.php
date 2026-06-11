<?php

namespace App\Services\Scores;

use App\Models\Program;
use App\Models\Score;
use Illuminate\Http\UploadedFile;

class ScoreService
{
    public function create(array $data, Program $program): Score
    {
        /** @var UploadedFile $file */
        $file = $data['file'];

        $mimeType = $file->getMimeType();

        $path = $file->store('scores', 'public');

        return Score::create([
            'program_id' => $program->id,
            'title' => $data['title'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $mimeType,
        ]);
    }

    public function getScoresByProgram(Program $program)
    {
        return Score::query()
            ->where('program_id', $program->id)
            ->latest()
            ->get();
    }
}