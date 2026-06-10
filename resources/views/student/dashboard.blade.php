@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="content-transition">
    {{-- Welcome Header with XP Streak --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animate-fade-in-up">
        <div>
            <h1 class="display-6 fw-bold mb-1">
                Welcome back, <span class="gradient-text">{{ explode(' ', auth()->user()->name)[0] }}</span> 👋
            </h1>
            <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }} — Ready to learn something new today?</p>
        </div>
        <div class="d-flex gap-3 mt-3 mt-md-0">
            <div class="streak-flame">
                <span class="flame-icon">🔥</span>
                <span><strong>7</strong> Day Streak</span>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-premium">
                <i class="bi bi-compass me-1"></i> Browse Courses
            </a>
        </div>
    </div>

    {{-- XP & Level Bar --}}
    <div class="glass-card p-4 mb-4 animate-fade-in-up delay-1">
        <div class="row align-items-center g-3">
            <div class="col-md-auto text-center">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                     style="width: 70px; height: 70px; background: var(--primary-gradient);">
                    <span class="text-white fw-bold fs-4">Lv.3</span>
                </div>
            </div>
            <div class="col-md">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold">Scholar Level</span>
                    <span class="text-muted small">1,250 / 2,000 XP</span>
                </div>
                <div class="xp-bar-container" style="height: 12px;">
                    <div class="xp-bar-fill" style="width: 62%;"></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">🔥 7-day streak</small>
                    <small class="text-muted">🏆 5 achievements</small>
                    <small class="text-muted">⭐ 240 XP this week</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6 animate-fade-in-up delay-1">
            <div class="stat-card-premium" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                    <div>
                        <p class="text-white-50 small mb-1">Enrolled</p>
                        <h3 class="text-white fw-bold mb-0">{{ $totalEnrollments }}</h3>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-journal-check text-white fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 animate-fade-in-up delay-2">
            <div class="stat-card-premium" style="background: linear-gradient(135deg, #06B6D4, #3B82F6);">
                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                    <div>
                        <p class="text-white-50 small mb-1">Completed</p>
                        <h3 class="text-white fw-bold mb-0">{{ $completedCourses }}</h3>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle text-white fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 animate-fade-in-up delay-3">
            <div class="stat-card-premium" style="background: linear-gradient(135deg, #F59E0B, #F97316);">
                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                    <div>
                        <p class="text-white-50 small mb-1">Avg Score</p>
                        <h3 class="text-white fw-bold mb-0">{{ number_format($averageScore ?? 0, 1) }}%</h3>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-trophy text-white fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 animate-fade-in-up delay-4">
            <div class="stat-card-premium" style="background: linear-gradient(135deg, #10B981, #059669);">
                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                    <div>
                        <p class="text-white-50 small mb-1">Certificates</p>
                        <h3 class="text-white fw-bold mb-0">{{ $certificates->count() }}</h3>
                    </div>
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-award text-white fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Enrolled Courses --}}
            <div class="glass-card p-4 mb-4 animate-fade-in-up delay-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-book me-2" style="color: #4F46E5;"></i>My Courses</h5>
                    <a href="{{ route('enrollments.index') }}" class="btn btn-sm btn-glass">View All</a>
                </div>
                <div class="row g-3">
                    @forelse($enrollments as $enrollment)
                        <div class="col-md-6">
                            <div class="course-card-premium">
                                <div class="card-img-wrapper" style="height: 120px; background: linear-gradient(135deg, rgba(79,70,229,0.2), rgba(124,58,237,0.2));">
                                    <div class="card-img-overlay-gradient"></div>
                                    <div class="card-level-badge">{{ ucfirst($enrollment->status) }}</div>
                                    <div class="position-absolute top-50 start-50 translate-middle">
                                        <i class="bi bi-play-circle text-white fs-2"></i>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h6 class="fw-bold mb-2" style="font-size: 0.9rem;">{{ $enrollment->course->title }}</h6>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Progress</span>
                                        <span class="fw-medium">{{ $enrollment->completion_percentage }}%</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $enrollment->completion_percentage }}%; background: var(--primary-gradient);"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="d-flex align-items-center gap-1">
                                            @if($enrollment->status === 'active')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 0.7rem;">
                                                    ● Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill" style="font-size: 0.7rem;">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('courses.show', $enrollment->course) }}" class="btn btn-sm btn-premium">
                                            Continue <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-book fs-1 d-block mb-3 text-muted"></i>
                            <p class="text-muted mb-3">Not enrolled in any courses yet.</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-premium">
                                Browse Courses <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Learning Roadmap (mini) --}}
            <div class="glass-card p-4 mb-3 animate-fade-in-up delay-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-signpost-2 me-2" style="color: #4F46E5;"></i>Quick Stats</h5>
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Overall Progress</span>
                            <span class="fw-medium">{{ $totalEnrollments > 0 ? round(($completedCourses / max($totalEnrollments, 1)) * 100) : 0 }}%</span>
                        </div>
                        <div class="xp-bar-container" style="height: 8px;">
                            <div class="xp-bar-fill" style="width: {{ $totalEnrollments > 0 ? round(($completedCourses / max($totalEnrollments, 1)) * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span><i class="bi bi-book me-1"></i>Enrolled: {{ $totalEnrollments }}</span>
                        <span><i class="bi bi-check-circle me-1 text-success"></i>Done: {{ $completedCourses }}</span>
                    </div>
                </div>
            </div>

            {{-- Recent Quiz Results --}}
            <div class="glass-card p-4 mb-3 animate-fade-in-up delay-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2" style="color: #F59E0B;"></i>Recent Quizzes</h5>
                @forelse($quizResults as $result)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <div>
                            <p class="mb-0 small fw-medium">{{ $result->quiz->title ?? 'Quiz' }}</p>
                            <small class="text-muted">{{ $result->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge rounded-pill bg-{{ $result->status === 'passed' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $result->status === 'passed' ? 'success' : 'danger' }}">
                            {{ $result->percentage }}%
                        </span>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-pencil fs-3 d-block mb-2"></i>
                        <small>No quiz attempts yet</small>
                    </div>
                @endforelse
            </div>

            {{-- Certificates --}}
            <div class="glass-card p-4 animate-fade-in-up delay-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-award me-2" style="color: #10B981;"></i>Certificates</h5>
                @forelse($certificates as $cert)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <div>
                            <p class="mb-0 small fw-medium">{{ $cert->course->title ?? 'Course' }}</p>
                            <small class="text-muted">{{ $cert->issue_date->format('M d, Y') }}</small>
                        </div>
                        <a href="{{ route('certificates.show', $cert) }}" class="btn btn-sm btn-glass">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-award fs-3 d-block mb-2"></i>
                        <small>No certificates yet</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
