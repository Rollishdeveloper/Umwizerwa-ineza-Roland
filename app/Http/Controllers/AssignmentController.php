<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ActivityLog;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index(Course $course)
    {
        $assignments = $course->assignments()->latest()->get();
        return view('assignments.index', compact('course', 'assignments'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:now',
            'total_marks' => 'required|numeric|min:1',
        ]);

        $assignment = $course->assignments()->create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created assignment {$assignment->title}"
        ]);

        return back()->with('success', 'Assignment created successfully.');
    }

    public function show(Course $course, Assignment $assignment)
    {
        $assignment->load('submissions.student');
        return view('assignments.show', compact('course', 'assignment'));
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:now',
            'total_marks' => 'required|numeric|min:1',
        ]);

        $assignment->update($validated);
        return back()->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('courses.show', $course)
            ->with('success', 'Assignment deleted successfully.');
    }

    public function submit(Request $request, Course $course, Assignment $assignment)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return back()->with('error', 'Only students can submit assignments.');
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,zip|max:20480',
            'notes' => 'nullable|string',
        ]);

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->assignment_id,
                'student_id' => $student->student_id,
            ],
            [
                'file_path' => $request->file('file')->store('assignments/submissions', 'public'),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        // Award points for submitting an assignment
        $this->gamificationService->handleAssignmentSubmission($student);

        return back()->with('success', 'Assignment submitted successfully.');
    }

    public function grade(Request $request, Course $course, Assignment $assignment, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'marks' => 'required|numeric|min:0|max:' . $assignment->total_marks,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'marks' => $validated['marks'],
            'feedback' => $validated['feedback'] ?? null,
        ]);

        return back()->with('success', 'Assignment graded successfully.');
    }
}
