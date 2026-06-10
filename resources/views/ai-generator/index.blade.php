@extends('layouts.app')
@section('title', 'AI Course Generator')
@section('content')
<div class="fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-robot text-primary me-2"></i>AI Course Generator</h4>
            <p class="text-muted mb-0">Upload educational materials and let AI build complete courses automatically</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ai-generator.history') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-clock-history"></i> History
            </a>
            <a href="{{ route('ai-generator.upload') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-cloud-arrow-up"></i> Upload Material
            </a>
        </div>
    </div>

    <!-- How It Works -->
    <div class="card stat-card mb-4">
        <div class="card-body">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-cloud-arrow-up-fill text-primary fs-3"></i>
                        </div>
                        <h6 class="fw-bold small">1. Upload Content</h6>
                        <small class="text-muted">Upload PDFs, documents, or presentations</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-magic text-warning fs-3"></i>
                        </div>
                        <h6 class="fw-bold small">2. AI Processing</h6>
                        <small class="text-muted">AI analyzes and structures the content</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-eye text-info fs-3"></i>
                        </div>
                        <h6 class="fw-bold small">3. Review & Edit</h6>
                        <small class="text-muted">Preview, customize, and reorder content</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-rocket-takeoff text-success fs-3"></i>
                        </div>
                        <h6 class="fw-bold small">4. Generate Course</h6>
                        <small class="text-muted">One-click generation with full content</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50 small">Uploaded</h6>
                    <h2 class="fw-bold mb-0">{{ $materials->where('status', 'uploaded')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50 small">Processing</h6>
                    <h2 class="fw-bold mb-0">{{ $materials->where('status', 'processing')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50 small">Ready for Review</h6>
                    <h2 class="fw-bold mb-0">{{ $materials->where('status', 'processed')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="small">Courses Generated</h6>
                    <h2 class="fw-bold mb-0">{{ $materials->whereNotNull('course_id')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials Table -->
    <div class="card stat-card">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark me-2"></i>Uploaded Materials</h6>
            <a href="{{ route('ai-generator.upload') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Upload New</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th>Confidence</th>
                            <th>Course</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                            <tr>
                                <td class="fw-medium">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ str_contains($material->mime_type, 'pdf') ? 'bi-filetype-pdf text-danger' : (str_contains($material->mime_type, 'word') ? 'bi-filetype-docx text-primary' : (str_contains($material->mime_type, 'presentation') ? 'bi-filetype-pptx text-warning' : 'bi-file-earmark text-muted')) }}"></i>
                                        <span>{{ Str::limit($material->original_filename, 35) }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ strtoupper(pathinfo($material->original_filename, PATHINFO_EXTENSION)) }}</span></td>
                                <td>{{ $material->file_size ? number_format($material->file_size / 1024, 1) . ' KB' : '-' }}</td>
                                <td>
                                    @php
                                        $statusColors = ['uploaded' => 'secondary', 'processing' => 'info', 'processed' => 'success', 'failed' => 'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$material->status] ?? 'secondary' }}">
                                        {{ $material->status === 'processed' ? 'Ready' : ucfirst($material->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($material->ai_confidence)
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="progress flex-grow-1" style="height: 5px; max-width: 50px;">
                                                <div class="progress-bar bg-{{ $material->ai_confidence >= 80 ? 'success' : ($material->ai_confidence >= 50 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $material->ai_confidence }}%"></div>
                                            </div>
                                            <small>{{ $material->ai_confidence }}%</small>
                                        </div>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td>
                                    @if($material->course)
                                        <a href="{{ route('courses.show', $material->course) }}" class="text-decoration-none small">{{ Str::limit($material->course->title, 25) }}</a>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $material->created_at->diffForHumans() }}</small></td>
                                <td>
                                    @if($material->status === 'processed' && !$material->course_id)
                                        <a href="{{ route('ai-generator.preview', $material) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> Review</a>
                                    @elseif($material->course_id)
                                        <a href="{{ route('courses.show', $material->course) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-right"></i> View</a>
                                    @elseif($material->status === 'failed')
                                        <span class="text-muted small">Failed</span>
                                    @else
                                        <div class="spinner-border spinner-border-sm text-info" role="status"></div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-cloud-arrow-up fs-1 d-block mb-3"></i>
                                    <h5>No materials uploaded yet</h5>
                                    <p class="mb-3">Upload a PDF, document, or presentation to get started</p>
                                    <a href="{{ route('ai-generator.upload') }}" class="btn btn-primary">
                                        <i class="bi bi-cloud-arrow-up"></i> Upload Your First Material
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($materials, 'hasPages') && $materials->hasPages())
            <div class="card-footer bg-transparent">{{ $materials->links() }}</div>
        @endif
    </div>
</div>
@endsection
