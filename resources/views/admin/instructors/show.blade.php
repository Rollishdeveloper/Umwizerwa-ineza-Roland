@extends('layouts.app')

@section('title', "Instructor Profile - $instructor->name")

@section('content')
<div class="fade-in">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($instructor->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold">{{ $instructor->name }}</h5>
                    <p class="text-muted">{{ $instructor->specialization ?? 'Instructor' }}</p>
                    <p><i class="bi bi-envelope"></i> {{ $instructor->email }}</p>
                    <span class="badge bg-{{ $instructor->user->status === 'active' ? 'success' : 'secondary' }} fs-6">{{ ucfirst($instructor->user->status) }}</span>
                </div>
            </div>
            @if($instructor->biography)
                <div class="card stat-card">
                    <div class="card-body"><h6 class="fw-bold">Biography</h6><p class="text-muted">{{ $instructor->biography }}</p></div>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card stat-card">
                <div class="card-header bg-transparent border-0"><h6 class="fw-bold mb-0"><i class="bi bi-book me-2"></i>Courses ({{ $instructor->courses->count() }})</h6></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($instructor->courses as $course)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div><p class="mb-0 fw-medium">{{ $course->title }}</p>                                <small class="text-muted">{{ $course->category->category_name ?? 'Uncategorized' }} | {{ $course->enrollments_count ?? $course->enrollments()->count() }} students</small></div>
                                <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($course->status) }}</span>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No courses yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
