@extends('layouts.app')

@section('title', 'Quizzes')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Quizzes</h4><p class="text-muted mb-0">{{ $course->title }}</p></div>
        <a href="{{ route('quizzes.create', $course) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Create Quiz</a>
    </div>
    <div class="row g-4">
        @forelse($quizzes as $quiz)
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold">{{ $quiz->title }}</h6>
                            <span class="badge bg-primary">{{ $quiz->questions_count ?? $quiz->questions()->count() }} Qs</span>
                        </div>
                        <p class="text-muted small mb-2">{{ Str::limit($quiz->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small><i class="bi bi-clock"></i> {{ $quiz->duration_minutes ?? 'N/A' }} min | Pass: {{ $quiz->passing_marks }}/{{ $quiz->total_marks }}</small>
                            <a href="{{ route('quizzes.show', [$course, $quiz]) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted"><i class="bi bi-pencil-square fs-1 d-block mb-3"></i><p>No quizzes yet</p></div>
        @endforelse
    </div>
</div>
@endsection
