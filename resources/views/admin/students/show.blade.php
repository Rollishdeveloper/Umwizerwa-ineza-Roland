@extends('layouts.app')

@section('title', "Student Profile - $student->name")

@section('content')
<div class="fade-in">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold">{{ $student->name }}</h5>
                    <p class="text-muted">{{ $student->email }}</p>
                    <p class="mb-1"><i class="bi bi-person-badge"></i> {{ $student->student_number }}</p>
                    <p class="mb-1"><i class="bi bi-telephone"></i> {{ $student->phone ?? 'N/A' }}</p>
                    <p><i class="bi bi-geo-alt"></i> {{ $student->address ?? 'N/A' }}</p>
                    <span class="badge bg-{{ $student->user->status === 'active' ? 'success' : 'secondary' }} fs-6">
                        {{ ucfirst($student->user->status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card stat-card mb-4">
                <div class="card-header bg-transparent border-0"><h6 class="fw-bold mb-0"><i class="bi bi-book me-2"></i>Enrolled Courses</h6></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($student->enrollments as $enrollment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $enrollment->course->title }}</p>
                                        <small class="text-muted">Enrolled: {{ $enrollment->enrollment_date->format('M d, Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $enrollment->completion_percentage }}%</span>
                                            <div class="progress" style="width: 100px;">
                                                <div class="progress-bar" style="width: {{ $enrollment->completion_percentage }}%"></div>
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'secondary' }} mt-1">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">Not enrolled in any courses</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-4">
                <div class="card-header bg-transparent border-0"><h6 class="fw-bold mb-0"><i class="bi bi-award me-2"></i>Certificates</h6></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($student->certificates as $cert)
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="fw-medium">{{ $cert->course->title }}</span>
                                <small class="text-muted">{{ $cert->issue_date->format('M d, Y') }}</small>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No certificates yet</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card stat-card">
                <div class="card-header bg-transparent border-0"><h6 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Quiz Results</h6></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($student->quizResults as $result)
                            <div class="list-group-item d-flex justify-content-between">
                                <div>
                                    <p class="mb-0 fw-medium">{{ $result->quiz->title }}</p>
                                    <small class="text-muted">{{ $result->percentage }}%</small>
                                </div>
                                <span class="badge bg-{{ $result->status === 'passed' ? 'success' : 'danger' }} align-self-center">
                                    {{ $result->status }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No quiz attempts</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
