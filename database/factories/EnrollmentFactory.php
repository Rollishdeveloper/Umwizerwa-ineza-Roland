<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'course_id' => Course::factory(),
            'enrollment_date' => now(),
            'completion_percentage' => fake()->numberBetween(0, 100),
            'status' => fake()->randomElement(['active', 'completed', 'dropped']),
        ];
    }
}
