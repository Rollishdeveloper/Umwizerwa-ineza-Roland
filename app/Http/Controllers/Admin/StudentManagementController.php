<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = Student::with('user', 'enrollments.course')->latest()->paginate(15);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => Str::slug($validated['name']) . '-' . Str::random(4),
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU-' . strtoupper(Str::random(8)),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created student account for {$student->name}"
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully. Default password: password123');
    }

    public function edit(Student $student)
    {
        $student->load('user');
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $student->update($validated);
        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Updated student {$student->name}'s information"
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->user->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Deleted student {$student->name}"
        ]);
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function show(Student $student)
    {
        $student->load('user', 'enrollments.course', 'quizResults.quiz', 'certificates');
        return view('admin.students.show', compact('student'));
    }
}
