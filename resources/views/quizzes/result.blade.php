@extends('layouts.app')

@section('title', 'Quiz Result')

@section('content')
<div class="fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card stat-card text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        @if($result->status === 'passed')
                            <div class="display-1 text-success"><i class="bi bi-trophy-fill"></i></div>
                            <h3 class="fw-bold text-success mt-3">Congratulations!</h3>
                        @else
                            <div class="display-1 text-danger"><i class="bi bi-x-circle-fill"></i></div>
                            <h3 class="fw-bold text-danger mt-3">Better Luck Next Time</h3>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-4">{{ $quiz->title }}</h5>

                    <div class="row justify-content-center g-3 mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="display-6 fw-bold text-primary">{{ number_format($result->percentage, 1) }}%</div>
                                <small class="text-muted">Score</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="display-6 fw-bold text-success">{{ $result->score }} / {{ $quiz->total_marks }}</div>
                                <small class="text-muted">Marks</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="display-6 fw-bold">{{ $quiz->passing_marks }}</div>
                                <small class="text-muted">Passing Marks</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <div class="display-6 fw-bold text-{{ $result->status === 'passed' ? 'success' : 'danger' }}">
                                    {{ ucfirst($result->status) }}
                                </div>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
