<?php

namespace Database\Factories;

use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentSubmissionFactory extends Factory
{
    protected $model = AssignmentSubmission::class;

    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => Student::factory(),
            'file_path' => 'assignments/submissions/test.pdf',
            'submitted_at' => now(),
            'marks' => null,
            'feedback' => null,
        ];
    }
}
