<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'repertoire' => $this->faker->sentence(),
            'type' => 'concert',
            'location' => $this->faker->city(),
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
        ];
    }
}
