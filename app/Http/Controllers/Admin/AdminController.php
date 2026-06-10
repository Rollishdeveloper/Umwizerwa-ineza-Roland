<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = Student::count();
        $totalInstructors = Instructor::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $totalCertificates = Certificate::count();
        $recentActivities = ActivityLog::with('user')->latest()->take(10)->get();
        $enrollmentsByMonth = Enrollment::selectRaw("strftime('%m', enrollment_date) as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $completionStats = Enrollment::selectRaw("
            CASE 
                WHEN completion_percentage = 100 THEN 'completed'
                WHEN completion_percentage >= 50 THEN 'halfway'
                ELSE 'started'
            END as status,
            count(*) as total
        ")->groupBy('status')->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalInstructors', 'totalCourses',
            'totalEnrollments', 'totalCertificates', 'recentActivities',
            'enrollmentsByMonth', 'completionStats'
        ));
    }

    public function users()
    {
        $users = User::with(['student', 'instructor'])->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate(['status' => 'required|in:active,inactive,suspended']);
        $user->update(['status' => $request->status]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Updated user {$user->name}'s status to {$request->status}"
        ]);
        return back()->with('success', 'User status updated successfully.');
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('POST') && $request->has('clear_cache')) {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            return back()->with('success', 'All application caches cleared successfully!');
        }

        return view('admin.settings');
    }
}
