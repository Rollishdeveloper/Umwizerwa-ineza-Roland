<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstructorManagementController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with('user', 'courses')->latest()->paginate(15);
        return view('admin.instructors.index', compact('instructors'));
    }

    public function create()
    {
        return view('admin.instructors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'specialization' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => Str::slug($validated['name']) . '-' . Str::random(4),
            'password' => Hash::make('password123'),
            'role' => 'instructor',
        ]);

        $instructor = Instructor::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'specialization' => $validated['specialization'] ?? null,
            'biography' => $validated['biography'] ?? null,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created instructor account for {$instructor->name}"
        ]);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor created successfully. Default password: password123');
    }

    public function edit(Instructor $instructor)
    {
        $instructor->load('user');
        return view('admin.instructors.edit', compact('instructor'));
    }

    public function update(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $instructor->user_id,
            'specialization' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $instructor->update($validated);
        $instructor->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Updated instructor {$instructor->name}'s information"
        ]);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->user->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Deleted instructor {$instructor->name}"
        ]);
        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor deleted successfully.');
    }

    public function show(Instructor $instructor)
    {
        $instructor->load('user', 'courses');
        $instructor->courses->loadCount('enrollments');
        return view('admin.instructors.show', compact('instructor'));
    }
}
