<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\ActivityLog;
use App\Services\AICourseGeneratorService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    protected $aiGenerator;

    public function __construct(AICourseGeneratorService $aiGenerator)
    {
        $this->aiGenerator = $aiGenerator;
    }

    public function index(Request $request)
    {
        $query = QuestionBank::with('course', 'creator');

        if ($request->filled('search')) {
            $query->where('question_text', 'like', "%{$request->search}%");
        }
        if ($request->filled('type')) {
            $query->where('question_type', $request->type);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $questions = $query->latest()->paginate(20);
        $courses = Course::published()->get();
        $types = ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'];

        return view('question-bank.index', compact('questions', 'courses', 'types'));
    }

    public function create()
    {
        $courses = Course::published()->get();
        $types = ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'];
        return view('question-bank.create', compact('courses', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,course_id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,fill_blank,matching,short_answer,essay',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:1',
            'option_a' => 'nullable|string|required_if:question_type,mcq,true_false',
            'option_b' => 'nullable|string|required_if:question_type,mcq,true_false',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'topic' => 'nullable|string',
        ]);

        $options = null;
        if (in_array($validated['question_type'], ['mcq', 'true_false'])) {
            $options = [
                'a' => $validated['option_a'] ?? '',
                'b' => $validated['option_b'] ?? '',
                'c' => $validated['option_c'] ?? '',
                'd' => $validated['option_d'] ?? '',
            ];
        }

        $question = QuestionBank::create([
            'course_id' => $validated['course_id'] ?? null,
            'user_id' => auth()->id(),
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
            'difficulty' => $validated['difficulty'],
            'marks' => $validated['marks'],
            'topic' => $validated['topic'] ?? null,
            'status' => 'draft',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Added question to question bank: {$question->question_type} - " . Str::limit($question->question_text, 50),
        ]);

        return redirect()->route('question-bank.index')->with('success', 'Question added to bank successfully.');
    }

    public function show(QuestionBank $question)
    {
        $question->load('course', 'creator');
        return view('question-bank.show', compact('question'));
    }

    public function edit(QuestionBank $question)
    {
        $courses = Course::published()->get();
        $types = ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'];
        return view('question-bank.edit', compact('question', 'courses', 'types'));
    }

    public function update(Request $request, QuestionBank $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,fill_blank,matching,short_answer,essay',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|numeric|min:1',
            'status' => 'required|in:draft,approved,rejected',
        ]);

        $question->update($validated);

        return redirect()->route('question-bank.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(QuestionBank $question)
    {
        $question->delete();
        return redirect()->route('question-bank.index')->with('success', 'Question deleted.');
    }

    public function approve(QuestionBank $question)
    {
        $question->update(['status' => 'approved']);
        return back()->with('success', 'Question approved.');
    }

    public function generateForCourse(Course $course)
    {
        $questions = $this->aiGenerator->generateQuizQuestions($course->title, 10);

        foreach ($questions as $qData) {
            QuestionBank::create([
                'course_id' => $course->course_id,
                'user_id' => auth()->id(),
                'question_text' => $qData['question_text'],
                'question_type' => $qData['question_type'],
                'options' => $qData['options'] ? json_decode($qData['options'], true) : null,
                'correct_answer' => $qData['correct_answer'],
                'explanation' => $qData['explanation'],
                'difficulty' => $qData['difficulty'],
                'marks' => $qData['marks'],
                'topic' => $course->title,
                'status' => 'draft',
            ]);
        }

        return redirect()->route('question-bank.index', ['course_id' => $course->course_id])
            ->with('success', 'Generated 10 questions for course: ' . $course->title);
    }
}
