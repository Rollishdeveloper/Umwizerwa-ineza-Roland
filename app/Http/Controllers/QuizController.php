<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizResult;
use App\Models\ActivityLog;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index(Course $course)
    {
        $quizzes = $course->quizzes()->withCount('questions')->get();
        return view('quizzes.index', compact('course', 'quizzes'));
    }

    public function create(Course $course)
    {
        return view('quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:1|lte:total_marks',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $quiz = $course->quizzes()->create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created quiz {$quiz->title} for course {$course->title}"
        ]);

        return redirect()->route('quizzes.show', [$course, $quiz])
            ->with('success', 'Quiz created successfully. Now add some questions.');
    }

    public function show(Course $course, Quiz $quiz)
    {
        $quiz->load('questions');
        return view('quizzes.show', compact('course', 'quiz'));
    }

    public function edit(Course $course, Quiz $quiz)
    {
        return view('quizzes.edit', compact('course', 'quiz'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:1|lte:total_marks',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $quiz->update($validated);
        return redirect()->route('quizzes.show', [$course, $quiz])
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('courses.show', $course)
            ->with('success', 'Quiz deleted successfully.');
    }

    public function takeQuiz(Course $course, Quiz $quiz)
    {
        $quiz->load('questions');
        return view('quizzes.take', compact('course', 'quiz'));
    }

    public function submitQuiz(Request $request, Course $course, Quiz $quiz)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return back()->with('error', 'Only students can take quizzes.');
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|in:a,b,c,d,true,false',
        ]);

        $quiz->load('questions');
        $correctCount = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $userAnswer = $request->answers[$question->question_id] ?? null;
            if ($userAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }

        $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        $scorePerQuestion = $totalQuestions > 0 ? $quiz->total_marks / $totalQuestions : 0;
        $score = $correctCount * $scorePerQuestion;
        $status = $score >= $quiz->passing_marks ? 'passed' : 'failed';

        $result = QuizResult::create([
            'quiz_id' => $quiz->quiz_id,
            'student_id' => $student->student_id,
            'score' => $score,
            'percentage' => $percentage,
            'status' => $status,
        ]);

        // Award points for quiz completion
        $passed = $status === 'passed';
        $perfect = $correctCount === $totalQuestions && $totalQuestions > 0;
        $this->gamificationService->handleQuizCompletion($student, $passed, $perfect);

        return redirect()->route('quizzes.result', [$course, $quiz, $result])
            ->with('success', 'Quiz submitted successfully!');
    }

    public function result(Course $course, Quiz $quiz, QuizResult $result)
    {
        return view('quizzes.result', compact('course', 'quiz', 'result'));
    }

    public function addQuestion(Request $request, Course $course, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,fill_blank,matching,short_answer,essay',
            'option_a' => 'required_if:question_type,mcq|required_if:question_type,true_false|nullable|string|max:255',
            'option_b' => 'required_if:question_type,mcq|required_if:question_type,true_false|nullable|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d,true,false',
            'explanation' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:1',
        ]);

        $quiz->questions()->create($validated);

        return back()->with('success', 'Question added successfully.');
    }

    public function updateQuestion(Request $request, Course $course, Quiz $quiz, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,fill_blank,matching,short_answer,essay',
            'option_a' => 'required_if:question_type,mcq|required_if:question_type,true_false|nullable|string|max:255',
            'option_b' => 'required_if:question_type,mcq|required_if:question_type,true_false|nullable|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d,true,false',
            'explanation' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:1',
        ]);

        $question->update($validated);

        return back()->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Course $course, Quiz $quiz, Question $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted successfully.');
    }
}
