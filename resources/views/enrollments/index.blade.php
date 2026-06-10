@extends('layouts.app')

@section('title', 'My Enrollments')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">My Enrollments</h4><p class="text-muted mb-0">Track your enrolled courses</p></div>
        <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-compass"></i> Browse Courses</a>
    </div>

    <div class="row g-4">
        @forelse($enrollments as $enrollment)
            <div class="col-md-6 col-lg-4">
                <div class="card course-card h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">{{ $enrollment->course->title }}</h6>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Progress</span>
                            <span class="fw-medium">{{ $enrollment->completion_percentage }}%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-{{ $enrollment->completion_percentage == 100 ? 'success' : ($enrollment->completion_percentage >= 50 ? 'warning' : 'primary') }}" 
                                 style="width: {{ $enrollment->completion_percentage }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><small class="text-muted"><i class="bi bi-calendar"></i> {{ $enrollment->enrollment_date->format('M d, Y') }}</small></span>
                            <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($enrollment->status) }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route('courses.show', $enrollment->course) }}" class="btn btn-outline-primary btn-sm w-100">Continue <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-journal fs-1 d-block mb-3"></i>
                <h5>Not enrolled in any courses</h5>
                <p>Browse our catalog and start learning today!</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary"><i class="bi bi-compass"></i> Browse Courses</a>
            </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $enrollments->links() }}</div>
</div>
@endsection
