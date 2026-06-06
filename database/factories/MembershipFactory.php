<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\User;
use App\Models\Orchestra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'orchestra_id' => Orchestra::factory(),
            'role' => 'member',
            'member_type' => 'core',
            'instrument' => null,
            'section' => null,
            'joined_at' => now(),
        ];
    }

    public function adminWithoutOrchestra(): static
    {
        return $this->state(fn() => [
            'orchestra_id' => null,
            'role' => 'admin',
        ]);
    }
}
