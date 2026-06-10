<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;

class StudentsExport
{
    public function collection(): Collection
    {
        return Student::with('user', 'enrollments', 'quizResults', 'certificates')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Email', 'Student Number', 'Phone', 'Gender',
            'Enrollments', 'Completed Courses', 'Avg Quiz Score', 'Certificates', 'Points', 'Level', 'Status', 'Created At'
        ];
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->collection() as $student) {
            $data[] = [
                $student->student_id,
                $student->name,
                $student->email,
                $student->student_number,
                $student->phone ?? '',
                $student->gender ?? '',
                $student->enrollments->count(),
                $student->enrollments->where('status', 'completed')->count(),
                $student->quizResults->avg('percentage') ? number_format($student->quizResults->avg('percentage'), 1) . '%' : 'N/A',
                $student->certificates->count(),
                $student->points ?? 0,
                $student->level ?? 1,
                $student->user->status ?? 'active',
                $student->created_at->format('Y-m-d'),
            ];
        }
        return $data;
    }
}
