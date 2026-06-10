<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\ApprovalWorkflow;
use App\Models\ContentVersion;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

class ApprovalWorkflowService
{
    /**
     * Get the next stage in the workflow.
     */
    public function getNextStage(string $currentStage): ?string
    {
        $stages = [
            'uploaded' => 'ai_generated',
            'ai_generated' => 'pending_review',
            'pending_review' => 'instructor_review',
            'instructor_review' => 'coordinator_review',
            'coordinator_review' => 'admin_approval',
            'admin_approval' => 'published',
        ];

        return $stages[$currentStage] ?? null;
    }

    /**
     * Advance workflow to next stage.
     */
    public function advanceStage(Course $course): ApprovalWorkflow
    {
        $workflow = $course->approvalWorkflow ?? ApprovalWorkflow::create([
            'course_id' => $course->course_id,
            'current_stage' => 'uploaded',
        ]);

        $nextStage = $this->getNextStage($workflow->current_stage);
        if ($nextStage) {
            $workflow->update(['current_stage' => $nextStage]);
        }

        return $workflow->fresh();
    }

    /**
     * Submit review for a course at a specific level.
     */
    public function submitReview(Course $course, int $reviewerId, string $reviewType, string $status, ?string $comments = null, ?array $detailedComments = []): CourseReview
    {
        $review = CourseReview::create([
            'course_id' => $course->course_id,
            'reviewer_id' => $reviewerId,
            'review_type' => $reviewType,
            'comments' => $comments,
            'status' => $status,
            'reviewed_at' => now(),
        ]);

        foreach ($detailedComments as $dc) {
            $review->comments()->create([
                'content_type' => $dc['content_type'] ?? 'course',
                'content_id' => $dc['content_id'] ?? null,
                'comment' => $dc['comment'] ?? '',
                'severity' => $dc['severity'] ?? 'info',
            ]);
        }

        $workflow = $course->approvalWorkflow;

        if ($status === 'approved') {
            $nextStage = $this->getNextStage($workflow->current_stage ?? 'ai_generated');
            if ($nextStage) {
                $workflow->update(['current_stage' => $nextStage]);
            }

            // If this is admin approval, publish course
            if ($reviewType === 'admin' && $status === 'approved') {
                $course->update(['status' => 'published']);
                $workflow->update(['current_stage' => 'published', 'completed_at' => now()]);

                $this->createVersion($course, $reviewerId, 'published', 'Course published after admin approval');
            } else {
                $this->createVersion($course, $reviewerId, $reviewType === 'instructor' ? 'instructor_reviewed' : 'coordinator_approved', ucfirst($reviewType) . ' approved the course');
            }

            $this->notifyStakeholders($course, "Course Approved", "Course '{$course->title}' has been approved at {$reviewType} level.");
        } else {
            $workflow->update([
                'current_stage' => $status === 'rejected' ? 'rejected' : 'instructor_review',
                'rejection_reason' => $comments,
                'suggested_corrections' => collect($detailedComments)->pluck('comment')->implode("\n"),
            ]);

            if ($status === 'rejected') {
                $course->update(['status' => 'draft']);
            }

            $this->notifyStakeholders($course, 'Course Requires Changes', "Course '{$course->title}' requires changes: {$comments}");
        }

        ActivityLog::create([
            'user_id' => $reviewerId,
            'activity' => "{$reviewType} review for '{$course->title}': {$status}",
        ]);

        return $review;
    }

    /**
     * Run automated content validation checks.
     */
    public function runValidationChecks(Course $course): array
    {
        $validator = app(AICourseGeneratorService::class);
        $result = $validator->validateGeneratedContent($course);

        // Calculate AI confidence based on validation
        $aiConfidence = $result['score'];

        // Run additional checks
        $result['duplicate_questions'] = $this->checkDuplicateQuestions($course);
        $result['missing_answers'] = $this->checkMissingAnswers($course);
        $result['ai_confidence'] = $aiConfidence;

        // Update workflow
        $workflow = $course->approvalWorkflow;
        if ($workflow) {
            $workflow->update(['current_stage' => 'pending_review']);
        }

        return $result;
    }

    private function checkDuplicateQuestions(Course $course): array
    {
        $duplicates = [];
        $seen = [];

        foreach ($course->quizzes as $quiz) {
            foreach ($quiz->questions as $question) {
                $normalized = preg_replace('/\s+/', ' ', strtolower(trim($question->question_text)));
                if (isset($seen[$normalized])) {
                    $duplicates[] = [
                        'question_id' => $question->question_id,
                        'text' => $question->question_text,
                        'duplicate_of' => $seen[$normalized],
                    ];
                }
                $seen[$normalized] = $question->question_id;
            }
        }

        return $duplicates;
    }

    private function checkMissingAnswers(Course $course): array
    {
        $missing = [];
        foreach ($course->quizzes as $quiz) {
            foreach ($quiz->questions as $question) {
                if ($question->question_type === 'mcq' && empty($question->option_a)) {
                    $missing[] = ['question_id' => $question->question_id, 'issue' => 'Missing options'];
                }
            }
        }
        return $missing;
    }

    private function createVersion(Course $course, int $userId, string $status, string $changes): void
    {
        $latestVersion = ContentVersion::where('course_id', $course->course_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $versionNum = $latestVersion ? $this->incrementVersion($latestVersion->version_number) : '1.0';

        ContentVersion::create([
            'course_id' => $course->course_id,
            'version_number' => $versionNum,
            'changes' => $changes,
            'created_by' => $userId,
            'status' => $status,
            'snapshot' => [
                'title' => $course->title,
                'description' => $course->description,
                'status' => $course->status,
                'updated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    private function incrementVersion(string $version): string
    {
        $parts = explode('.', $version);
        $minor = (int)($parts[1] ?? 0) + 1;
        return "{$parts[0]}.{$minor}";
    }

    private function notifyStakeholders(Course $course, string $title, string $message): void
    {
        $users = collect();

        // Notify course instructor
        if ($course->instructor && $course->instructor->user) {
            $users->push($course->instructor->user);
        }

        // Notify all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        $users = $users->merge($admins);

        foreach ($users->unique('id') as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => 'approval',
                'status' => 'unread',
            ]);
        }
    }

    /**
     * Get analytics for admin dashboard.
     */
    public function getAnalytics(): array
    {
        return [
            'total_generated' => Course::where('status', '!=', 'draft')->count(),
            'pending_review' => ApprovalWorkflow::pending()->count(),
            'approved' => ApprovalWorkflow::atStage('published')->count(),
            'rejected' => ApprovalWorkflow::atStage('rejected')->count(),
            'avg_review_time' => $this->getAverageReviewTime(),
            'ai_accuracy' => $this->getAIConfidenceAverage(),
        ];
    }

    private function getAverageReviewTime(): float
    {
        return CourseReview::whereNotNull('reviewed_at')
            ->where('created_at', '!=', now())
            ->get()
            ->avg(function ($review) {
                return $review->reviewed_at ? $review->reviewed_at->diffInHours($review->created_at) : 0;
            }) ?? 0;
    }

    private function getAIConfidenceAverage(): float
    {
        return ContentVersion::whereNotNull('ai_confidence')
            ->avg('ai_confidence') ?? 0;
    }
}
