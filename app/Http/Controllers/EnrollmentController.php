<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        if (auth()->user()->isStudent()) {
            $student = auth()->user()->student;
            $enrollments = Enrollment::with('course')
                ->where('student_id', $student->student_id ?? 0)
                ->latest()
                ->paginate(10);
        } else {
            $enrollments = Enrollment::with('student', 'course')
                ->latest()
                ->paginate(15);
        }

        return view('enrollments.index', compact('enrollments'));
    }

    public function store(Request $request, Course $course)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return back()->with('error', 'Only students can enroll in courses.');
        }

        $existing = Enrollment::where('student_id', $student->student_id)
            ->where('course_id', $course->course_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        $enrollment = Enrollment::create([
            'student_id' => $student->student_id,
            'course_id' => $course->course_id,
            'status' => 'active',
        ]);

        // Award points for enrolling
        $this->gamificationService->awardPoints(
            $student,
            GamificationService::POINTS_ENROLL_COURSE,
            'Enrolled in a course'
        );

        Notification::create([
            'user_id' => $course->instructor->user_id,
            'title' => 'New Enrollment',
            'message' => "{$student->name} has enrolled in {$course->title}",
            'type' => 'enrollment',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Enrolled in course {$course->title}"
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Successfully enrolled in the course!')
            ->with('gamification', true);
    }

    public function updateProgress(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'completion_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $enrollment->update([
            'completion_percentage' => $request->completion_percentage,
            'status' => $request->completion_percentage >= 100 ? 'completed' : 'active',
        ]);

        // Handle course completion
        if ($request->completion_percentage >= 100) {
            $student = $enrollment->student;
            $this->gamificationService->handleCourseCompletion($student);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->update(['status' => 'dropped']);
        return back()->with('success', 'Enrollment cancelled.');
    }
}
