<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Lesson;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'practice_exercises' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
            'document' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'duration' => 'nullable|integer|min:0',
            'position' => 'nullable|integer',
        ]);

        $validated['position'] = $validated['position'] ?? $module->lessons()->count() + 1;

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')
                ->store('lessons/documents', 'public');
        }

        $lesson = $module->lessons()->create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created lesson {$lesson->title}"
        ]);

        return back()->with('success', 'Lesson created successfully.');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load('module.course');
        return view('lessons.show', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'practice_exercises' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'audio_url' => 'nullable|url|max:500',
            'document' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'duration' => 'nullable|integer|min:0',
            'position' => 'nullable|integer',
        ]);

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')
                ->store('lessons/documents', 'public');
        }

        $lesson->update($validated);

        return back()->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return back()->with('success', 'Lesson deleted successfully.');
    }
}
