<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'course_id' => Course::factory(),
            'certificate_number' => 'CERT-' . strtoupper(Str::random(12)),
            'issue_date' => now(),
        ];
    }
}
