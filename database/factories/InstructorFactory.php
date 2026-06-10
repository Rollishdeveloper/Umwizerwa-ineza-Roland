<?php

namespace Database\Factories;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstructorFactory extends Factory
{
    protected $model = Instructor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'specialization' => fake()->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Literature']),
            'biography' => fake()->paragraph(),
        ];
    }
}
