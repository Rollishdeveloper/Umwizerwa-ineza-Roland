@extends('layouts.app')

@section('title', 'Manage Enrollments')

@section('content')
<div class="fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-journal-check me-2"></i>Manage Enrollments</h4>
            <p class="text-muted mb-0">View and manage student enrollments in your courses</p>
        </div>
        <a href="{{ route('instructor.add-enrollment') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Add Enrollment
        </a>
    </div>

    <!-- Course Stats -->
    <div class="row g-3 mb-4">
        @forelse($courses as $course)
            <div class="col-md-3 col-6">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <h6 class="fw-bold small mb-1">{{ Str::limit($course->title, 20) }}</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $course->enrollments_count }}</h3>
                        <small class="text-muted">Students</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">You don't have any courses yet. <a href="{{ route('courses.create') }}">Create one</a></div>
            </div>
        @endforelse
    </div>

    <!-- Enrollments Table -->
    <div class="card stat-card">
        <div class="card-header bg-transparent border-0">
            <h6 class="fw-bold mb-0"><i class="bi bi-list-ol me-2"></i>All Enrollments</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Enrolled Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->enrollment_id ?? $loop->iteration }}</td>
                                <td class="fw-medium">{{ $enrollment->student->name ?? 'N/A' }}</td>
                                <td>{{ $enrollment->course->title ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small" style="min-width: 35px;">{{ $enrollment->completion_percentage }}%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                                            <div class="progress-bar bg-{{ $enrollment->completion_percentage == 100 ? 'success' : ($enrollment->completion_percentage >= 50 ? 'warning' : 'primary') }}" 
                                                 style="width: {{ $enrollment->completion_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><small class="text-muted">{{ $enrollment->enrollment_date->format('M d, Y') }}</small></td>
                                <td>
                                    <form action="{{ route('instructor.destroy-enrollment', $enrollment) }}" method="POST" 
                                          onsubmit="return confirm('Drop this student from the course?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Drop Enrollment">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No enrollments found in your courses.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            {{ $enrollments->links() }}
        </div>
    </div>
</div>
@endsection
