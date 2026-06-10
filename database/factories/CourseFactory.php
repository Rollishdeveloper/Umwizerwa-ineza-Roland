<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        return [
            'instructor_id' => Instructor::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'description' => fake()->paragraph(),
            'category_id' => Category::factory(),
            'duration' => fake()->numberBetween(30, 120),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'price' => fake()->randomFloat(2, 0, 100),
            'status' => fake()->randomElement(['draft', 'published']),
        ];
    }
}
