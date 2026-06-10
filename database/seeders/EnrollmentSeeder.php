<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('No students or courses found. Skipping enrollments.');
            return;
        }

        $this->command->info('Creating 1000+ enrollments...');

        $existingPairs = [];
        $enrollments = [];
        $totalEnrollments = 0;
        $targetEnrollments = 1000;

        // Ensure every student is enrolled in at least 2-5 courses
        foreach ($students as $student) {
            $numCourses = rand(2, 5);
            $availableCourses = $courses->random(min($numCourses, $courses->count()));

            foreach ($availableCourses as $course) {
                $pairKey = "{$student->student_id}-{$course->course_id}";
                if (isset($existingPairs[$pairKey])) {
                    continue;
                }
                $existingPairs[$pairKey] = true;

                $completionPercentage = rand(0, 100);
                $status = $completionPercentage >= 100 ? 'completed' : (rand(0, 5) === 0 ? 'dropped' : 'active');

                $enrollments[] = [
                    'student_id' => $student->student_id,
                    'course_id' => $course->course_id,
                    'enrollment_date' => Carbon::now()->subDays(rand(1, 180)),
                    'completion_percentage' => $completionPercentage,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalEnrollments++;

                // Insert in batches of 100
                if (count($enrollments) >= 100) {
                    Enrollment::insert($enrollments);
                    $enrollments = [];
                }
            }
        }

        // Fill remaining to reach 1000 with random enrollments
        while ($totalEnrollments < $targetEnrollments) {
            $student = $students->random();
            $course = $courses->random();
            $pairKey = "{$student->student_id}-{$course->course_id}";

            if (isset($existingPairs[$pairKey])) {
                continue;
            }
            $existingPairs[$pairKey] = true;

            $completionPercentage = rand(0, 100);
            $status = $completionPercentage >= 100 ? 'completed' : (rand(0, 5) === 0 ? 'dropped' : 'active');

            $enrollments[] = [
                'student_id' => $student->student_id,
                'course_id' => $course->course_id,
                'enrollment_date' => Carbon::now()->subDays(rand(1, 180)),
                'completion_percentage' => $completionPercentage,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $totalEnrollments++;

            if (count($enrollments) >= 100) {
                Enrollment::insert($enrollments);
                $enrollments = [];
            }
        }

        // Insert remaining
        if (!empty($enrollments)) {
            Enrollment::insert($enrollments);
        }

        $this->command->info("Created {$totalEnrollments} enrollments!");
    }
}
