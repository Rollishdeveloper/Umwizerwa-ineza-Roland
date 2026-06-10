<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\QuizResult;
use App\Models\Certificate;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->student_id)
            ->latest()
            ->take(6)
            ->get();

        $totalEnrollments = Enrollment::where('student_id', $student->student_id)->count();
        $completedCourses = Enrollment::where('student_id', $student->student_id)
            ->where('status', 'completed')->count();

        $quizResults = QuizResult::with('quiz')
            ->where('student_id', $student->student_id)
            ->latest()
            ->take(5)
            ->get();

        $certificates = Certificate::with('course')
            ->where('student_id', $student->student_id)
            ->latest()
            ->get();

        $pendingAssignments = AssignmentSubmission::where('student_id', $student->student_id)
            ->whereNull('marks')
            ->count();

        $averageScore = QuizResult::where('student_id', $student->student_id)
            ->avg('percentage');

        return view('student.dashboard', compact(
            'enrollments', 'totalEnrollments', 'completedCourses',
            'quizResults', 'certificates', 'pendingAssignments', 'averageScore'
        ));
    }
}
