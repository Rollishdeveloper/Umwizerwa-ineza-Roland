@extends('layouts.app')

@section('title', 'System Reports')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">System Reports</h4><p class="text-muted mb-0">System-wide statistics</p></div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card stat-card bg-primary text-white"><div class="card-body"><h6 class="text-white-50">Total Users</h6><h2 class="fw-bold mb-0">{{ $totalUsers }}</h2></div></div></div>
        <div class="col-md-3"><div class="card stat-card bg-success text-white"><div class="card-body"><h6 class="text-white-50">Students</h6><h2 class="fw-bold mb-0">{{ $totalStudents }}</h2></div></div></div>
        <div class="col-md-3"><div class="card stat-card bg-warning text-white"><div class="card-body"><h6 class="text-white-50">Instructors</h6><h2 class="fw-bold mb-0">{{ $totalInstructors }}</h2></div></div></div>
        <div class="col-md-3"><div class="card stat-card bg-info text-white"><div class="card-body"><h6 class="text-white-50">Enrollments</h6><h2 class="fw-bold mb-0">{{ $totalEnrollments }}</h2></div></div></div>
    </div>

    <div class="card stat-card">
        <div class="card-header bg-transparent border-0"><h6 class="fw-bold mb-0"><i class="bi bi-activity me-2"></i>Recent System Activities</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Time</th><th>User</th><th>Activity</th></tr></thead>
                    <tbody>
                        @foreach($activities as $log)
                            <tr>
                                <td>{{ $log->created_at->diffForHumans() }}</td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td>{{ $log->activity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
