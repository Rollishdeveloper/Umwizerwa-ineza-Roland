<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\ApprovalWorkflow;
use App\Services\ApprovalWorkflowService;
use App\Services\AICourseGeneratorService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalWorkflow;
    protected $aiGenerator;

    public function __construct(ApprovalWorkflowService $approvalWorkflow, AICourseGeneratorService $aiGenerator)
    {
        $this->approvalWorkflow = $approvalWorkflow;
        $this->aiGenerator = $aiGenerator;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $query = ApprovalWorkflow::with('course.instructor');

        if ($user->isInstructor()) {
            $query->whereHas('course', function ($q) use ($user) {
                $q->where('instructor_id', $user->instructor->instructor_id ?? 0);
            });
        }

        // Filter by stage if requested
        if (request('stage')) {
            $query->where('current_stage', request('stage'));
        }

        $workflows = $query->latest()->paginate(20);
        
        $stats = [
            'pending' => ApprovalWorkflow::pending()->count(),
            'published' => ApprovalWorkflow::atStage('published')->count(),
            'rejected' => ApprovalWorkflow::atStage('rejected')->count(),
            'needs_review' => ApprovalWorkflow::whereIn('current_stage', ['pending_review', 'instructor_review', 'coordinator_review', 'admin_approval'])->count(),
        ];

        return view('approval.dashboard', compact('workflows', 'stats'));
    }

    public function review(Course $course)
    {
        $course->load('modules.lessons', 'quizzes.questions', 'assignments', 'finalExams.questions', 'approvalWorkflow', 'instructor', 'category');

        // Run validation
        $validation = $this->aiGenerator->validateGeneratedContent($course);

        return view('approval.review', compact('course', 'validation'));
    }

    public function submitReview(Request $request, Course $course)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,revision',
            'comments' => 'nullable|string',
            'review_type' => 'required|in:instructor,coordinator,admin',
            'detailed_comments' => 'nullable|array',
            'detailed_comments.*.content_type' => 'required|in:course,module,lesson,quiz,question,assignment,exam',
            'detailed_comments.*.comment' => 'required|string',
            'detailed_comments.*.severity' => 'required|in:info,warning,error,critical',
        ]);

        $status = $validated['action'] === 'approve' ? 'approved' : ($validated['action'] === 'reject' ? 'rejected' : 'revision_required');

        $this->approvalWorkflow->submitReview(
            $course,
            auth()->id(),
            $validated['review_type'],
            $status,
            $validated['comments'] ?? null,
            $validated['detailed_comments'] ?? []
        );

        $message = $status === 'approved' ? 'Course approved successfully!' : ($status === 'rejected' ? 'Course rejected.' : 'Revision requested.');

        return redirect()->route('approval.dashboard')->with('success', $message);
    }

    public function queue()
    {
        $query = ApprovalWorkflow::with('course.instructor', 'course.category')
            ->whereIn('current_stage', ['pending_review', 'instructor_review', 'coordinator_review', 'admin_approval']);

        $user = auth()->user();
        if ($user->isAdmin()) {
            $query->where('current_stage', 'admin_approval');
        }

        // Filter by priority
        if (request('priority')) {
            $query->where('priority', request('priority'));
        }

        $queue = $query->latest()->paginate(20);

        return view('approval.queue', compact('queue'));
    }

    public function versions(Course $course)
    {
        $versions = $course->contentVersions()->with('creator')->latest()->get();
        return view('approval.versions', compact('course', 'versions'));
    }

    public function analytics()
    {
        $analytics = $this->approvalWorkflow->getAnalytics();
        
        $workflowsByStage = ApprovalWorkflow::selectRaw('current_stage, count(*) as total')
            ->groupBy('current_stage')
            ->pluck('total', 'current_stage');

        $reviewsByType = CourseReview::selectRaw('review_type, status, count(*) as total')
            ->groupBy('review_type', 'status')
            ->get();

        return view('approval.analytics', compact('analytics', 'workflowsByStage', 'reviewsByType'));
    }
}
