<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinalExam;
use App\Models\FinalExamResult;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class FinalExamController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function show(Course $course)
    {
        $exam = $course->finalExams()->with('questions')->first();

        if (!$exam) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'No final exam available for this course yet.');
        }

        $student = auth()->user()->student;
        $previousAttempts = collect();
        $bestResult = null;

        if ($student) {
            $previousAttempts = FinalExamResult::where('exam_id', $exam->exam_id)
                ->where('student_id', $student->student_id)
                ->orderBy('attempt', 'desc')
                ->get();

            $bestResult = $previousAttempts->sortByDesc('percentage')->first();

            $attemptsUsed = $previousAttempts->count();
            if ($attemptsUsed >= $exam->attempts_allowed) {
                return view('quizzes.final_exam_show', compact('course', 'exam', 'previousAttempts', 'bestResult'))
                    ->with('error', 'You have used all your allowed attempts for this exam.');
            }
        }

        return view('quizzes.final_exam_show', compact('course', 'exam', 'previousAttempts', 'bestResult'));
    }

    public function take(Course $course, FinalExam $exam)
    {
        $exam->load('questions');

        $student = auth()->user()->student;
        if (!$student) {
            return back()->with('error', 'Only students can take exams.');
        }

        $attemptsUsed = FinalExamResult::where('exam_id', $exam->exam_id)
            ->where('student_id', $student->student_id)
            ->count();

        if ($attemptsUsed >= $exam->attempts_allowed) {
            return redirect()->route('final-exams.show', $course)
                ->with('error', 'You have used all your allowed attempts.');
        }

        return view('quizzes.final_exam_take', compact('course', 'exam', 'attemptsUsed'));
    }

    public function submit(Request $request, Course $course, FinalExam $exam)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return back()->with('error', 'Only students can submit exams.');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|in:a,b,c,d',
        ]);

        $exam->load('questions');

        $correctCount = 0;
        $totalQuestions = $exam->questions->count();

        foreach ($exam->questions as $question) {
            $userAnswer = $request->answers[$question->question_id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }

        $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        $scorePerQuestion = $totalQuestions > 0 ? $exam->total_marks / $totalQuestions : 0;
        $score = $correctCount * $scorePerQuestion;
        $status = $score >= $exam->passing_marks ? 'passed' : 'failed';

        $attemptsUsed = FinalExamResult::where('exam_id', $exam->exam_id)
            ->where('student_id', $student->student_id)
            ->count();

        $result = FinalExamResult::create([
            'exam_id' => $exam->exam_id,
            'student_id' => $student->student_id,
            'score' => $score,
            'percentage' => $percentage,
            'status' => $status,
            'attempt' => $attemptsUsed + 1,
        ]);

        // If passed, award points
        if ($status === 'passed') {
            $this->gamificationService->handleCourseCompletion($student);
        }

        return redirect()->route('final-exams.result', [$course, $exam, $result])
            ->with('success', $status === 'passed'
                ? 'Congratulations! You passed the final exam!'
                : 'You did not pass this attempt. You have ' . ($exam->attempts_allowed - $attemptsUsed - 1) . ' attempt(s) remaining.');
    }

    public function result(Course $course, FinalExam $exam, FinalExamResult $result)
    {
        return view('quizzes.final_exam_result', compact('course', 'exam', 'result'));
    }
}
