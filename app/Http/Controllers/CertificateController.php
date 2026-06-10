<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ActivityLog;
use App\Models\Enrollment;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        if (auth()->user()->isStudent()) {
            $student = auth()->user()->student;
            $certificates = Certificate::with('course')
                ->where('student_id', $student->student_id ?? 0)
                ->latest()
                ->get();
        } else {
            $certificates = Certificate::with('student', 'course')
                ->latest()
                ->paginate(15);
        }

        return view('certificates.index', compact('certificates'));
    }

    public function generate(Enrollment $enrollment)
    {
        if ($enrollment->completion_percentage < 100) {
            return back()->with('error', 'Course not yet completed.');
        }

        $existing = Certificate::where('student_id', $enrollment->student_id)
            ->where('course_id', $enrollment->course_id)
            ->first();

        if ($existing) {
            return redirect()->route('certificates.show', $existing);
        }

        $certificate = Certificate::create([
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'certificate_number' => 'CERT-' . strtoupper(Str::random(12)),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Generated certificate for course completion"
        ]);

        $enrollment->update(['status' => 'completed']);

        // Award points and check achievements for earning a certificate
        $student = $enrollment->student;
        $this->gamificationService->handleCertificateEarned($student);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Certificate generated successfully!');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('student', 'course');
        return view('certificates.show', compact('certificate'));
    }

    public function verify(Request $request)
    {
        $request->validate(['certificate_number' => 'required|string']);
        $certificate = Certificate::with('student', 'course')
            ->where('certificate_number', $request->certificate_number)
            ->first();

        if (!$certificate) {
            return back()->with('error', 'Invalid certificate number.');
        }

        return view('certificates.verify', compact('certificate'));
    }
}
