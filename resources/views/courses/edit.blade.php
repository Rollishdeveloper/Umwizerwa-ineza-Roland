@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Course</h4>
            <p class="text-muted mb-0">{{ $course->title }}</p>
        </div>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Course Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $course->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Description</label>
                            <textarea name="description" class="form-control" rows="6">{{ old('description', $course->description) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Category</label>
                                <select name="category_id" class="form-select">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ $course->category_id == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Level</label>
                                <select name="level" class="form-select">
                                    @foreach(['beginner', 'intermediate', 'advanced', 'all'] as $level)
                                        <option value="{{ $level }}" {{ $course->level === $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Status</label>
                                <select name="status" class="form-select">
                                    <option value="draft" {{ $course->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ $course->status === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ $course->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Duration (minutes)</label>
                                <input type="number" name="duration" class="form-control" value="{{ old('duration', $course->duration) }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Price ($)</label>
                                <input type="number" name="price" class="form-control" value="{{ old('price', $course->price) }}" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Thumbnail</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        </div>
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" class="img-fluid rounded mb-2">
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
