<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Orchestra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');

        return [
            'name' => $this->faker->sentence(3),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween(
                $startDate,
                $startDate->modify('+1 week')
            )->format('Y-m-d'),
            'orchestra_id' => Orchestra::factory(),
        ];
    }
}
