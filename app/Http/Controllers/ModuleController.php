<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $modules = $course->modules()->with('lessons')->get();
        return view('modules.index', compact('course', 'modules'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
        ]);

        $validated['position'] = $validated['position'] ?? $course->modules()->count() + 1;

        $module = $course->modules()->create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created module {$module->title} in course {$course->title}"
        ]);

        return back()->with('success', 'Module created successfully.');
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
        ]);

        $module->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Updated module {$module->title}"
        ]);

        return back()->with('success', 'Module updated successfully.');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return back()->with('success', 'Module deleted successfully.');
    }
}
