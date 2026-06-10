<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3) . ' Quiz',
            'description' => fake()->paragraph(),
            'total_marks' => fake()->randomFloat(2, 10, 100),
            'passing_marks' => fake()->randomFloat(2, 5, 50),
            'duration_minutes' => fake()->numberBetween(10, 60),
        ];
    }
}
