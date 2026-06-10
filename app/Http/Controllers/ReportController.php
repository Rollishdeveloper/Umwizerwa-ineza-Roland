<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\ActivityLog;
use App\Exports\StudentsExport;
use App\Exports\CoursesExport;
use App\Exports\InstructorsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function students()
    {
        $students = Student::with('user', 'enrollments', 'quizResults', 'certificates')->get();
        return view('reports.students', compact('students'));
    }

    public function courses()
    {
        $courses = Course::with('instructor', 'category', 'enrollments')->get();
        return view('reports.courses', compact('courses'));
    }

    public function instructors()
    {
        $instructors = Instructor::with('user', 'courses.enrollments')->get();
        return view('reports.instructors', compact('instructors'));
    }

    public function system()
    {
        $totalUsers = \App\Models\User::count();
        $totalStudents = Student::count();
        $totalInstructors = Instructor::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $activities = ActivityLog::with('user')->latest()->take(50)->get();

        return view('reports.system', compact(
            'totalUsers', 'totalStudents', 'totalInstructors',
            'totalCourses', 'totalEnrollments', 'activities'
        ));
    }

    /**
     * Export PDF report.
     */
    public function exportPdf($type)
    {
        switch ($type) {
            case 'students':
                $students = Student::with('user', 'enrollments', 'quizResults', 'certificates')->get();
                $pdf = Pdf::loadView('reports.pdf.students', compact('students'));
                return $pdf->download('students-report-' . now()->format('Y-m-d') . '.pdf');

            case 'courses':
                $courses = Course::with('instructor', 'category', 'enrollments')->get();
                $pdf = Pdf::loadView('reports.pdf.courses', compact('courses'));
                return $pdf->download('courses-report-' . now()->format('Y-m-d') . '.pdf');

            case 'certificates':
                $certificates = Certificate::with('student', 'course')->latest()->get();
                $pdf = Pdf::loadView('reports.pdf.certificates', compact('certificates'));
                return $pdf->download('certificates-report-' . now()->format('Y-m-d') . '.pdf');

            default:
                return back()->with('error', 'Invalid report type for PDF export.');
        }
    }

    /**
     * Export CSV report.
     */
    public function exportCsv($type)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $type . '-report-' . now()->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            switch ($type) {
                case 'students':
                    $export = new StudentsExport();
                    fputcsv($file, $export->headings());
                    foreach ($export->toArray() as $row) {
                        fputcsv($file, $row);
                    }
                    break;

                case 'courses':
                    $export = new CoursesExport();
                    fputcsv($file, $export->headings());
                    foreach ($export->toArray() as $row) {
                        fputcsv($file, $row);
                    }
                    break;

                case 'instructors':
                    $export = new InstructorsExport();
                    fputcsv($file, $export->headings());
                    foreach ($export->toArray() as $row) {
                        fputcsv($file, $row);
                    }
                    break;

                default:
                    fputcsv($file, ['Invalid export type']);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Excel (CSV-based, compatible with Excel).
     */
    public function exportExcel($type)
    {
        return $this->exportCsv($type);
    }
}
