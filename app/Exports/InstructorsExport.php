<?php

namespace App\Exports;

use App\Models\Instructor;

class InstructorsExport
{
    public function toArray(): array
    {
        $instructors = Instructor::with('user', 'courses.enrollments')->get();
        $data = [];
        foreach ($instructors as $instructor) {
            $totalStudents = 0;
            $completedStudents = 0;
            foreach ($instructor->courses as $course) {
                $totalStudents += $course->enrollments->count();
                $completedStudents += $course->enrollments->where('status', 'completed')->count();
            }
            $avgCompletion = $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100, 1) : 0;

            $data[] = [
                $instructor->instructor_id,
                $instructor->name,
                $instructor->email,
                $instructor->specialization ?? 'N/A',
                $instructor->courses->count(),
                $totalStudents,
                $avgCompletion . '%',
                $instructor->user->status ?? 'active',
                $instructor->created_at->format('Y-m-d'),
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Email', 'Specialization', 'Courses',
            'Total Students', 'Avg Completion', 'Status', 'Created'
        ];
    }
}
