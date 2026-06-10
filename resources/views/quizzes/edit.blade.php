@extends('layouts.app')

@section('title', 'Edit Quiz')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Quiz</h4>
            <p class="text-muted mb-0">{{ $course->title }} - {{ $quiz->title }}</p>
        </div>
        <a href="{{ route('quizzes.show', [$course, $quiz]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('quizzes.update', [$course, $quiz]) }}">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Quiz Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $quiz->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $quiz->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Total Marks</label>
                            <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', $quiz->total_marks) }}" min="1" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Passing Marks</label>
                            <input type="number" name="passing_marks" class="form-control" value="{{ old('passing_marks', $quiz->passing_marks) }}" min="1" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('quizzes.show', [$course, $quiz]) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
