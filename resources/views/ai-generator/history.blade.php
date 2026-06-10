@extends('layouts.app')
@section('title', 'Generation History')
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2"></i>AI Generation History</h4>
            <p class="text-muted mb-0">Courses you've created using the AI generator</p>
        </div>
        <a href="{{ route('ai-generator.index') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-left"></i> Back to Generator</a>
    </div>

    <div class="card stat-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Modules</th>
                            <th>Lessons</th>
                            <th>Workflow Stage</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($generatedCourses as $course)
                            <tr>
                                <td class="fw-medium">{{ Str::limit($course->title, 40) }}</td>
                                <td>{{ $course->modules->count() }}</td>
                                <td>{{ $course->modules->sum(fn($m) => $m->lessons->count()) }}</td>
                                <td>
                                    @if($course->approvalWorkflow)
                                        <span class="badge bg-{{ $course->approvalWorkflow->current_stage === 'published' ? 'success' : ($course->approvalWorkflow->current_stage === 'ai_generated' ? 'info' : 'warning') }}">
                                            {{ str_replace('_', ' ', ucfirst($course->approvalWorkflow->current_stage)) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">{{ ucfirst($course->status) }}</span></td>
                                <td><small class="text-muted">{{ $course->created_at->format('M d, Y') }}</small></td>
                                <td>
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-robot fs-1 d-block mb-3"></i>
                                    <h5>No courses generated yet</h5>
                                    <p>Use the AI Course Generator to create your first course.</p>
                                    <a href="{{ route('ai-generator.upload') }}" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Upload Material</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($generatedCourses, 'hasPages') && $generatedCourses->hasPages())
            <div class="card-footer bg-transparent">{{ $generatedCourses->links() }}</div>
        @endif
    </div>
</div>
@endsection
