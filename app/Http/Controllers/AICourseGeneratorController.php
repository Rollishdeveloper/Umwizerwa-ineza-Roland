<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UploadedMaterial;
use App\Services\AICourseGeneratorService;
use App\Services\ApprovalWorkflowService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AICourseGeneratorController extends Controller
{
    protected $aiGenerator;
    protected $approvalWorkflow;

    public function __construct(AICourseGeneratorService $aiGenerator, ApprovalWorkflowService $approvalWorkflow)
    {
        $this->aiGenerator = $aiGenerator;
        $this->approvalWorkflow = $approvalWorkflow;
    }

    public function index()
    {
        $materials = UploadedMaterial::where('user_id', auth()->id())
            ->with('course')
            ->latest()
            ->paginate(15);
        return view('ai-generator.index', compact('materials'));
    }

    public function uploadForm()
    {
        $categories = Category::all();
        return view('ai-generator.upload', compact('categories'));
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,txt,epub,png,jpg,jpeg|max:51200',
            'category_id' => 'required|exists:categories,category_id',
            'generation_mode' => 'nullable|in:quick,enhanced',
        ]);

        $lastMaterial = null;
        $combinedText = '';
        $skippedDuplicates = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // --- DUPLICATE DETECTION ---
            // Check if this user has already uploaded the exact same file
            // (same filename AND same file size = strong indicator of duplicate)
            $existing = UploadedMaterial::where('user_id', auth()->id())
                ->where('original_filename', $originalName)
                ->where('file_size', $fileSize)
                ->whereNotNull('extracted_text')
                ->latest()
                ->first();

            if ($existing && $existing->status === 'processed') {
                $skippedDuplicates[] = $originalName;
                $combinedText .= $existing->extracted_text . "\n\n--- NEXT DOCUMENT ---\n\n";
                $lastMaterial = $existing;
                continue;
            }

            $path = $file->store('uploads/materials', 'local');

            $material = UploadedMaterial::create([
                'user_id' => auth()->id(),
                'original_filename' => $originalName,
                'stored_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $fileSize,
                'status' => 'uploaded',
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => "Uploaded material for AI generation: {$material->original_filename}",
            ]);

            // Process each material
            $result = $this->aiGenerator->processUploadedMaterial($material);

            if ($result['success']) {
                $combinedText .= $material->extracted_text . "\n\n--- NEXT DOCUMENT ---\n\n";
            }

            $lastMaterial = $material;
        }

        if (!$lastMaterial) {
            return redirect()->route('ai-generator.index')
                ->with('error', 'No files were uploaded successfully.');
        }

        // For multi-document uploads, combine all extracted text into the last material
        if (count($request->file('files')) > 1) {
            $lastMaterial->update([
                'extracted_text' => $combinedText,
                'metadata' => array_merge(
                    json_decode($lastMaterial->metadata ?? '{}', true) ?: [],
                    ['multi_document' => true, 'file_count' => count($request->file('files'))]
                ),
            ]);
            // Re-analyze with combined text
            $analysis = $this->aiGenerator->analyzeContent($combinedText);
            $lastMaterial->update([
                'metadata' => $analysis,
                'ai_confidence' => $analysis['confidence'],
            ]);
        }

        $successMessage = 'Material' . (count($request->file('files')) > 1 ? 's' : '') . ' processed successfully! Review the generated course structure below.';

        if (!empty($skippedDuplicates)) {
            $count = count($skippedDuplicates);
            $names = implode(', ', $skippedDuplicates);
            $successMessage .= " (Note: {$count} duplicate file(s) detected — {$names} was already uploaded previously. Reusing existing processed content.)";
        }

        return redirect()->route('ai-generator.preview', [
            'material' => $lastMaterial,
            'category_id' => $validated['category_id'],
        ])->with('success', $successMessage);
    }

    public function preview(UploadedMaterial $material, Request $request)
    {
        if ($material->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $analysis = $this->aiGenerator->analyzeContent($material->extracted_text ?? '');
        $structure = $this->aiGenerator->generateCourseStructure($analysis);
        $categories = Category::all();
        $selectedCategory = $request->get('category_id');

        return view('ai-generator.preview', compact('material', 'analysis', 'structure', 'categories', 'selectedCategory'));
    }

    public function generate(Request $request, UploadedMaterial $material)
    {
        if ($material->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $instructor = auth()->user()->instructor;
        if (!$instructor) {
            return back()->with('error', 'Only instructors can generate courses.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
        ]);

        if ($material->status !== 'processed') {
            // Re-process if needed
            $this->aiGenerator->processUploadedMaterial($material);
        }

        $analysis = $this->aiGenerator->analyzeContent($material->extracted_text ?? '');
        $structure = $this->aiGenerator->generateCourseStructure($analysis);

        // Override title/description/difficulty if provided in the form
        if (!empty($validated['title'])) {
            $structure['title'] = $validated['title'];
        }
        if (!empty($validated['description'])) {
            $structure['description'] = $validated['description'];
        }
        if (!empty($validated['difficulty'])) {
            $structure['difficulty'] = $validated['difficulty'];
        }

        $course = $this->aiGenerator->createCourseFromStructure(
            $structure,
            $instructor->instructor_id,
            $validated['category_id'],
            $material
        );

        // Run validation
        $validation = $this->approvalWorkflow->runValidationChecks($course);

        // Advance workflow to AI generated stage
        $this->approvalWorkflow->advanceStage($course);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course generated successfully! The course is now pending review. Validation score: ' . $validation['score'] . '%');
    }

    public function history()
    {
        $generatedCourses = \App\Models\Course::where('instructor_id', auth()->user()->instructor->instructor_id ?? 0)
            ->with(['approvalWorkflow', 'modules.lessons'])
            ->latest()
            ->paginate(15);

        return view('ai-generator.history', compact('generatedCourses'));
    }

    public function generateVideoScript(UploadedMaterial $material)
    {
        if ($material->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $analysis = $this->aiGenerator->analyzeContent($material->extracted_text ?? '');
        $structure = $this->aiGenerator->generateCourseStructure($analysis);
        $scripts = $this->aiGenerator->generateVideoScripts($structure);

        return response()->json(['success' => true, 'scripts' => $scripts]);
    }

    public function generatePresentation(UploadedMaterial $material)
    {
        if ($material->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $analysis = $this->aiGenerator->analyzeContent($material->extracted_text ?? '');
        $structure = $this->aiGenerator->generateCourseStructure($analysis);
        $slides = $this->aiGenerator->generatePresentationSlides($structure);

        return response()->json(['success' => true, 'slides' => $slides]);
    }

    public function combineMaterials(Request $request)
    {
        $validated = $request->validate([
            'material_ids' => 'required|array',
            'material_ids.*' => 'exists:uploaded_materials,material_id',
            'category_id' => 'required|exists:categories,category_id',
        ]);

        $materials = UploadedMaterial::whereIn('material_id', $validated['material_ids'])
            ->where('user_id', auth()->id())
            ->get();

        $combinedText = '';
        foreach ($materials as $mat) {
            $combinedText .= ($mat->extracted_text ?? '') . "\n\n---\n\n";
        }

        $lastMaterial = $materials->last();
        $analysis = $this->aiGenerator->analyzeContent($combinedText);
        $lastMaterial->update([
            'extracted_text' => $combinedText,
            'metadata' => $analysis,
            'ai_confidence' => $analysis['confidence'],
        ]);

        return redirect()->route('ai-generator.preview', [
            'material' => $lastMaterial,
            'category_id' => $validated['category_id'],
        ])->with('success', 'Materials combined successfully!');
    }
}
