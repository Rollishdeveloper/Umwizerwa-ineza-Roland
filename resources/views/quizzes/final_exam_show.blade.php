@extends('layouts.app')

@section('title', 'Final Exam - ' . $course->title)

@section('content')
<div class="fade-in">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold mb-2"><i class="bi bi-pencil-square text-danger me-2"></i>{{ $exam->title }}</h4>
                            <p class="text-muted">{{ $course->title }}</p>
                        </div>
                        <span class="badge bg-danger fs-6">Final Exam</span>
                    </div>

                    <p>{{ $exam->description }}</p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fw-bold fs-4 text-primary">{{ $exam->questions->count() }}</div>
                                <small class="text-muted">Questions</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fw-bold fs-4 text-success">{{ $exam->total_marks }}</div>
                                <small class="text-muted">Total Marks</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fw-bold fs-4 text-warning">{{ $exam->passing_marks }}</div>
                                <small class="text-muted">Passing Marks</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fw-bold fs-4 text-info">{{ $exam->duration_minutes ?? 'N/A' }}</div>
                                <small class="text-muted">Duration (min)</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="badge bg-{{ $exam->auto_grade ? 'success' : 'secondary' }}">
                            <i class="bi bi-robot me-1"></i> {{ $exam->auto_grade ? 'Auto-graded' : 'Manual grading' }}
                        </span>
                        <span class="badge bg-info">
                            <i class="bi bi-arrow-repeat me-1"></i> {{ $exam->attempts_allowed }} attempt(s) allowed
                        </span>
                    </div>

                    @if(auth()->check() && auth()->user()->isStudent())
                        @php
                            $student = auth()->user()->student;
                            $attemptsUsed = $student ? \App\Models\FinalExamResult::where('exam_id', $exam->exam_id)
                                ->where('student_id', $student->student_id)->count() : 0;
                            $hasPassed = $student ? \App\Models\FinalExamResult::where('exam_id', $exam->exam_id)
                                ->where('student_id', $student->student_id)
                                ->where('status', 'passed')->exists() : false;
                        @endphp
                        @if($hasPassed)
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i> You have already passed this final exam!
                            </div>
                        @elseif($attemptsUsed < $exam->attempts_allowed)
                            <a href="{{ route('final-exams.take', [$course, $exam]) }}" class="btn btn-danger btn-lg w-100">
                                <i class="bi bi-play-circle"></i> Start Final Exam (Attempt {{ $attemptsUsed + 1 }} of {{ $exam->attempts_allowed }})
                            </a>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i> You have used all {{ $exam->attempts_allowed }} allowed attempts.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($previousAttempts->isNotEmpty())
                <div class="card stat-card">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Your Attempts</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($previousAttempts as $attempt)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Attempt #{{ $attempt->attempt }}</small>
                                            <div class="fw-medium {{ $attempt->status === 'passed' ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($attempt->percentage, 1) }}% - {{ ucfirst($attempt->status) }}
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $attempt->status === 'passed' ? 'success' : 'danger' }}">
                                            {{ $attempt->score }}/{{ $exam->total_marks }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
