<?php

namespace Database\Factories;

use App\Models\QuizResult;
use App\Models\Quiz;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizResultFactory extends Factory
{
    protected $model = QuizResult::class;

    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'student_id' => Student::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'percentage' => fake()->randomFloat(2, 0, 100),
            'status' => fake()->randomElement(['passed', 'failed']),
            'submitted_at' => now(),
        ];
    }
}
