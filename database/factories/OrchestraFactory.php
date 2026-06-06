<?php

namespace Database\Factories;

use App\Models\Orchestra;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrchestraFactory extends Factory
{
    protected $model = Orchestra::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'location' => $this->faker->city(),
        ];
    }
}