<?php

namespace App\Services\Scores;

use App\Models\Program;
use App\Models\Score;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

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

    private function sanitizeOriginalName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name);

        $name = str_replace(['/', '\\'], '', $name);

        $name = trim($name);

        $name = mb_substr($name, 0, 100);

        return $name;
    }
}
