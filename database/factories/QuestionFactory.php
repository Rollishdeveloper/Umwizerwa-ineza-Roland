<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        $options = [
            'a' => fake()->sentence(),
            'b' => fake()->sentence(),
            'c' => fake()->sentence(),
            'd' => fake()->sentence(),
        ];
        $correct = fake()->randomElement(['a', 'b', 'c', 'd']);

        return [
            'quiz_id' => Quiz::factory(),
            'question_text' => fake()->sentence() . '?',
            'option_a' => $options['a'],
            'option_b' => $options['b'],
            'option_c' => $options['c'],
            'option_d' => $options['d'],
            'correct_answer' => $correct,
        ];
    }
}
