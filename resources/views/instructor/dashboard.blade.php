@extends('layouts.app')

@section('title', 'Instructor Dashboard')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Instructor Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Create New Course
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">My Courses</h6>
                            <h2 class="fw-bold mb-0">{{ $courses->count() }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-book-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Active Students</h6>
                            <h2 class="fw-bold mb-0">{{ $totalStudents }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Enrollments</h6>
                            <h2 class="fw-bold mb-0">{{ $totalEnrollments }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-journal-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Pending Grades</h6>
                            <h2 class="fw-bold mb-0">{{ $pendingAssignments }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-file-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card stat-card">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-book me-2"></i>My Courses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                    <tr>
                                        <td>
                                            <a href="{{ route('courses.show', $course) }}" class="text-decoration-none fw-medium">
                                                {{ $course->title }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ ucfirst($course->level) }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $course->enrollments_count ?? 0 }}</td>
                                        <td>
                                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-book fs-3 d-block mb-2"></i>
                                            No courses yet. Create your first course!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Student Progress</h6>
                    <canvas id="progressChart"></canvas>
                </div>
            </div>

            <div class="card stat-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Submissions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentSubmissions as $submission)
                            <div class="list-group-item">
                                <p class="mb-0 small fw-medium">{{ $submission->student->name ?? 'Unknown' }}</p>
                                <small class="text-muted">{{ $submission->assignment->title ?? 'Unknown' }}</small>
                                <small class="text-muted d-block">{{ $submission->created_at->diffForHumans() }}</small>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                No recent submissions
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('progressChart')?.getContext('2d');
    if (ctx) {
        const data = {!! json_encode($enrollmentStats) !!};
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.status),
                datasets: [{
                    data: data.map(d => d.total),
                    backgroundColor: ['#28a745', '#ffc107', '#17a2b8'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                cutout: '65%',
            }
        });
    }
});
</script>
@endpush
