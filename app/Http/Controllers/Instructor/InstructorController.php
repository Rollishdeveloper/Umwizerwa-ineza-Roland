<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\Notification;
use App\Models\ActivityLog;

class InstructorController extends Controller
{
    public function dashboard()
    {
        $instructor = auth()->user()->instructor;

        if (!$instructor) {
            return redirect()->route('dashboard')->with('error', 'Instructor profile not found.');
        }

        $courses = Course::where('instructor_id', $instructor->instructor_id)
            ->withCount('enrollments')
            ->get();
        $courseIds = $courses->pluck('course_id');

        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')->count();

        $pendingAssignments = AssignmentSubmission::whereIn('assignment_id', 
            Assignment::whereIn('course_id', $courseIds)->pluck('assignment_id')
        )->whereNull('marks')->count();

        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();

        $recentSubmissions = AssignmentSubmission::with(['assignment', 'student'])
            ->whereIn('assignment_id', Assignment::whereIn('course_id', $courseIds)->pluck('assignment_id'))
            ->latest()
            ->take(10)
            ->get();

        $enrollmentStats = Enrollment::selectRaw("
            CASE 
                WHEN completion_percentage = 100 THEN 'completed'
                WHEN completion_percentage >= 50 THEN 'halfway'
                ELSE 'started'
            END as status,
            count(*) as total
        ")->whereIn('course_id', $courseIds)
        ->groupBy('status')->get();

        return view('instructor.dashboard', compact(
            'courses', 'totalStudents', 'pendingAssignments',
            'totalEnrollments', 'recentSubmissions', 'enrollmentStats'
        ));
    }

    public function enrollments()
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) {
            return redirect()->route('dashboard')->with('error', 'Instructor profile not found.');
        }

        $courseIds = Course::where('instructor_id', $instructor->instructor_id)->pluck('course_id');

        $enrollments = Enrollment::with(['student', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest()
            ->paginate(20);

        $courses = Course::where('instructor_id', $instructor->instructor_id)
            ->withCount('enrollments')
            ->get();

        return view('instructor.enrollments', compact('enrollments', 'courses'));
    }

    public function addEnrollmentForm()
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) {
            return redirect()->route('dashboard')->with('error', 'Instructor profile not found.');
        }

        $courses = Course::where('instructor_id', $instructor->instructor_id)->get();
        $students = Student::with('user')->orderBy('name')->get();

        return view('instructor.add-enrollment', compact('courses', 'students'));
    }

    public function storeEnrollment(Request $request)
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) {
            return back()->with('error', 'Instructor profile not found.');
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,course_id',
            'student_id' => 'required|exists:students,student_id',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        // Ensure instructor owns this course
        if ($course->instructor_id !== $instructor->instructor_id) {
            return back()->with('error', 'You can only enroll students in your own courses.');
        }

        // Check for duplicate
        $existing = Enrollment::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        Enrollment::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'status' => 'active',
        ]);

        $student = Student::find($validated['student_id']);

        Notification::create([
            'user_id' => $student->user_id,
            'title' => 'Enrolled in Course',
            'message' => "You have been enrolled in {$course->title} by your instructor.",
            'type' => 'enrollment',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Enrolled student {$student->name} in course {$course->title}"
        ]);

        return redirect()->route('instructor.enrollments')
            ->with('success', 'Student enrolled successfully.');
    }

    public function destroyEnrollment(Enrollment $enrollment)
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) {
            return back()->with('error', 'Instructor profile not found.');
        }

        $course = $enrollment->course;
        if ($course->instructor_id !== $instructor->instructor_id) {
            return back()->with('error', 'You can only manage enrollments in your own courses.');
        }

        $enrollment->update(['status' => 'dropped']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Dropped student from course {$course->title}"
        ]);

        return back()->with('success', 'Enrollment cancelled successfully.');
    }
}
