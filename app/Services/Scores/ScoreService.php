<?php

namespace App\Services\Scores;

use App\Models\Program;
use App\Models\Score;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ScoreService
{
    public function create(array $data, Program $program): Score
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $data['file'];

        $finfoMime = finfo_file(
            finfo_open(FILEINFO_MIME_TYPE),
            $file->getPathname()
        );

        if ($finfoMime !== 'application/pdf') {
            throw ValidationException::withMessages([
                'file' => 'Invalid file content',
            ]);
        }

        $originalName = $data['sanitized_original_name']
            ?? $file->getClientOriginalName();

        $path = $file->store('scores', 'private');

        return Score::create([
            'program_id' => $program->id,
            'file_path'  => $path,
            'original_name' => $originalName,
            'size'       => $file->getSize(),
            'mime_type'  => $finfoMime,
        ]);
    }

    public function getScoresByProgram(Program $program)
    {
        return Score::query()
            ->where('program_id', $program->id)
            ->latest()
            ->get();
    }

    public function downloadScore(Score $score)
    {
        if (! Storage::disk('private')->exists($score->file_path)) {
            abort(404);
        }

        return Storage::disk('private')->download(
            $score->file_path,
            $score->original_name,
            [
                'Content-Type' => $score->mime_type,
            ]
        );
    }

    public function delete(Score $score): void
    {
        Storage::disk('private')->delete($score->file_path);

        $score->delete();
    }
}
