@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="fade-in">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stat-card">
                <div class="card-body">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('courses.show', $lesson->module->course) }}">{{ $lesson->module->course->title }}</a></li>
                            <li class="breadcrumb-item">{{ $lesson->module->title }}</li>
                            <li class="breadcrumb-item active">{{ $lesson->title }}</li>
                        </ol>
                    </nav>

                    <h4 class="fw-bold mb-3">{{ $lesson->title }}</h4>

                    @if($lesson->video_url)
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen></iframe>
                        </div>
                    @endif

                    <div class="lesson-content mb-4">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>

                    @if($lesson->document_path)
                        <a href="{{ asset('storage/' . $lesson->document_path) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-file-pdf"></i> Download Document
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stat-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-list me-2"></i>Lesson Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Module</small>
                        <span class="fw-medium">{{ $lesson->module->title }}</span>
                    </div>
                    @if($lesson->duration)
                        <div class="mb-3">
                            <small class="text-muted d-block">Duration</small>
                            <span class="fw-medium">{{ $lesson->duration }} minutes</span>
                        </div>
                    @endif
                    @if($lesson->video_url)
                        <div class="mb-3">
                            <small class="text-muted d-block">Content Type</small>
                            <span class="badge bg-info"><i class="bi bi-camera-video"></i> Video</span>
                        </div>
                    @endif
                    @if($lesson->document_path)
                        <div class="mb-3">
                            <small class="text-muted d-block">Material</small>
                            <span class="badge bg-success"><i class="bi bi-file-pdf"></i> Document Available</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
