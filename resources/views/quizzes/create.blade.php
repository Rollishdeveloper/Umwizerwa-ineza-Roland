@extends('layouts.app')

@section('title', 'Create Quiz')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Create Quiz</h4>
            <p class="text-muted mb-0">{{ $course->title }}</p>
        </div>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('quizzes.store', $course) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Quiz Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Total Marks</label>
                            <input type="number" name="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', 100) }}" min="1" step="0.01" required>
                            @error('total_marks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Passing Marks</label>
                            <input type="number" name="passing_marks" class="form-control @error('passing_marks') is-invalid @enderror" value="{{ old('passing_marks', 50) }}" min="1" step="0.01" required>
                            @error('passing_marks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes') }}" min="1">
                            <small class="text-muted">Leave empty for no time limit</small>
                            @error('duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Create Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
