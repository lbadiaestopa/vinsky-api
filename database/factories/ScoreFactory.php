<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Score;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @extends Factory<Score>
 */
class ScoreFactory extends Factory
{
    public function definition(): array
    {
        $fileName = $this->faker->uuid() . '.pdf';

        return [
            'program_id' => Program::factory(),
            'title' => $this->faker->sentence(3),
            'original_name' => 'score.pdf',
            'mime_type' => 'application/pdf',
            'file_path' => 'scores/' . $fileName,
            'size' => $this->faker->numberBetween(10_000, 5_000_000),
        ];
    }
}