<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('instructor', 'category');

        // Enhanced search across multiple fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('learning_objectives', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Duration filter
        if ($request->filled('duration')) {
            switch ($request->duration) {
                case 'short':
                    $query->where('duration', '<', 30);
                    break;
                case 'medium':
                    $query->whereBetween('duration', [30, 60]);
                    break;
                case 'long':
                    $query->where('duration', '>', 60);
                    break;
            }
        }

        // Price filter
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('price', 0);
            } elseif ($request->price === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'latest');
        switch ($sortField) {
            case 'popular':
                $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
                break;
            case 'completion':
                $query->withCount(['enrollments as completion_rate' => function ($q) {
                    $q->where('completion_percentage', 100);
                }])->orderBy('completion_rate', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        if (auth()->check() && auth()->user()->isInstructor()) {
            $instructor = auth()->user()->instructor;
            if ($instructor) {
                $query->where('instructor_id', $instructor->instructor_id);
            }
        }

        $courses = $query->paginate(12);
        $categories = Category::all();

        // Recommendations for students
        $recommendedCourses = collect();
        if (auth()->check() && auth()->user()->isStudent()) {
            $student = auth()->user()->student;
            if ($student) {
                $enrolledCategoryIds = $student->enrollments()
                    ->with('course')
                    ->get()
                    ->pluck('course.category_id')
                    ->unique();

                if ($enrolledCategoryIds->isNotEmpty()) {
                    $recommendedCourses = Course::with('instructor')
                        ->whereIn('category_id', $enrolledCategoryIds)
                        ->whereNotIn('course_id', $student->enrollments()->pluck('course_id'))
                        ->published()
                        ->withCount('enrollments')
                        ->orderBy('enrollments_count', 'desc')
                        ->take(4)
                        ->get();
                }

                if ($recommendedCourses->isEmpty()) {
                    $recommendedCourses = Course::published()
                        ->withCount('enrollments')
                        ->orderBy('enrollments_count', 'desc')
                        ->take(4)
                        ->get();
                }
            }
        }

        return view('courses.index', compact('courses', 'categories', 'recommendedCourses'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,category_id',
            'duration' => 'nullable|integer',
            'level' => 'required|in:beginner,intermediate,advanced,all',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
        ]);

        $instructor = auth()->user()->instructor;
        if (!$instructor) {
            return back()->with('error', 'Only instructors can create courses.');
        }

        $validated['instructor_id'] = $instructor->instructor_id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course = Course::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created course {$course->title}"
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('instructor', 'category', 'modules.lessons', 'modules.learningMaterials', 'quizzes', 'assignments', 'finalExams.questions');
        $isEnrolled = false;
        if (auth()->check() && auth()->user()->isStudent()) {
            $student = auth()->user()->student;
            if ($student) {
                $isEnrolled = $course->enrollments()
                    ->where('student_id', $student->student_id)
                    ->exists();
            }
        }

        // Related courses from same category
        $relatedCourses = Course::where('category_id', $course->category_id)
            ->where('course_id', '!=', $course->course_id)
            ->published()
            ->with('instructor')
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(4)
            ->get();

        return view('courses.show', compact('course', 'isEnrolled', 'relatedCourses'));
    }

    public function edit(Course $course)
    {
        if (auth()->user()->isInstructor() && auth()->user()->instructor->instructor_id !== $course->instructor_id) {
            abort(403);
        }
        $categories = Category::all();
        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        if (auth()->user()->isInstructor() && auth()->user()->instructor->instructor_id !== $course->instructor_id) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,category_id',
            'duration' => 'nullable|integer',
            'level' => 'required|in:beginner,intermediate,advanced,all',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Updated course {$course->title}"
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if (auth()->user()->isInstructor() && auth()->user()->instructor->instructor_id !== $course->instructor_id) {
            abort(403);
        }
        $course->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Deleted course {$course->title}"
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
