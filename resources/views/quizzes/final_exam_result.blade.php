@extends('layouts.app')

@section('title', 'Final Exam Result - ' . $course->title)

@section('content')
<div class="fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card stat-card">
                <div class="card-body text-center py-5">
                    @if($result->status === 'passed')
                        <div class="display-1 text-success mb-3">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h3 class="fw-bold text-success">Congratulations! You Passed!</h3>
                        <p class="text-muted mb-4">You have successfully completed the final exam for <strong>{{ $course->title }}</strong></p>
                    @else
                        <div class="display-1 text-danger mb-3">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <h3 class="fw-bold text-danger">Exam Not Passed</h3>
                        <p class="text-muted mb-4">You did not meet the passing score for <strong>{{ $course->title }}</strong></p>
                    @endif

                    <div class="row g-3 justify-content-center mb-4">
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-4 {{ $result->status === 'passed' ? 'text-success' : 'text-danger' }}">{{ number_format($result->percentage, 1) }}%</div>
                                <small class="text-muted">Score</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-4 text-primary">{{ $result->score }}/{{ $exam->total_marks }}</div>
                                <small class="text-muted">Marks</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-4 text-warning">{{ $exam->passing_marks }}</div>
                                <small class="text-muted">Passing Marks</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-4 text-info">#{{ $result->attempt }}</div>
                                <small class="text-muted">Attempt</small>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $result->status === 'passed' ? 'success' : 'danger' }}" 
                             role="progressbar" style="width: {{ $result->percentage }}%">
                            {{ number_format($result->percentage, 1) }}%
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        @if($result->status === 'passed')
                            <a href="{{ route('certificates.index') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-award"></i> View My Certificates
                            </a>
                        @else
                            @php
                                $attemptsUsed = \App\Models\FinalExamResult::where('exam_id', $exam->exam_id)
                                    ->where('student_id', auth()->user()->student->student_id ?? 0)
                                    ->count();
                            @endphp
                            @if($attemptsUsed < $exam->attempts_allowed)
                                <a href="{{ route('final-exams.take', [$course, $exam]) }}" class="btn btn-danger btn-lg">
                                    <i class="bi bi-arrow-repeat"></i> Retry Exam
                                </a>
                            @endif
                        @endif
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left"></i> Back to Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
