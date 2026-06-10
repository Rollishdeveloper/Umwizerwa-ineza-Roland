<?php

namespace App\Exports;

use App\Models\Course;

class CoursesExport
{
    public function toArray(): array
    {
        $courses = Course::with('instructor', 'category', 'enrollments')->get();
        $data = [];
        foreach ($courses as $course) {
            $totalStudents = $course->enrollments->count();
            $completedStudents = $course->enrollments->where('status', 'completed')->count();
            $completionRate = $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100, 1) : 0;

            $data[] = [
                $course->course_id,
                $course->title,
                $course->instructor->name ?? 'N/A',
                $course->category->category_name ?? 'N/A',
                ucfirst($course->level),
                ucfirst($course->status),
                $totalStudents,
                $completedStudents,
                $completionRate . '%',
                $course->created_at->format('Y-m-d'),
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'ID', 'Title', 'Instructor', 'Category', 'Level',
            'Status', 'Total Students', 'Completed', 'Completion Rate', 'Created'
        ];
    }
}
