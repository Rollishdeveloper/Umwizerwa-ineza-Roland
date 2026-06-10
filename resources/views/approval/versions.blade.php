@extends('layouts.app')
@section('title', 'Version History - ' . $course->title)
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i>Version History</h4><p class="text-muted mb-0">{{ $course->title }}</p></div>
        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Course</a>
    </div>
    <div class="timeline">
        @forelse($versions as $version)
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $version->status === 'published' ? 'success' : ($version->status === 'ai_generated' ? 'info' : 'warning') }} text-white flex-shrink-0" style="width:40px;height:40px;">
                                <i class="bi bi-{{ $version->status === 'published' ? 'check' : ($version->status === 'ai_generated' ? 'robot' : 'eye') }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">v{{ $version->version_number }} - {{ str_replace('_', ' ', ucfirst($version->status)) }}</h6>
                                <p class="text-muted small mb-0">{{ $version->changes }}</p>
                                <small class="text-muted">By {{ $version->creator->name ?? 'System' }} | {{ $version->created_at->format('M d, Y H:i') }}</small>
                                @if($version->ai_confidence)
                                    <span class="badge bg-info ms-2">AI Confidence: {{ $version->ai_confidence }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted"><i class="bi bi-clock-history fs-1 d-block mb-3"></i><h5>No version history yet</h5></div>
        @endforelse
    </div>
</div>
<style>.timeline .card { border-left: 3px solid #667eea; }</style>
@endsection
