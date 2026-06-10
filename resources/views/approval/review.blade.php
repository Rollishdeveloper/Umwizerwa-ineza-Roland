@extends('layouts.app')
@section('title', 'Review Course - ' . $course->title)
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Review: {{ $course->title }}</h4><p class="text-muted mb-0">Review and approve content before publishing</p></div>
        <a href="{{ route('approval.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Course Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="small text-muted">Title</label><p class="fw-medium">{{ $course->title }}</p></div>
                        <div class="col-md-6"><label class="small text-muted">Category</label><p>{{ $course->category->category_name ?? 'N/A' }}</p></div>
                        <div class="col-md-6"><label class="small text-muted">Instructor</label><p>{{ $course->instructor->name ?? 'N/A' }}</p></div>
                        <div class="col-md-6"><label class="small text-muted">Duration</label><p>{{ $course->duration ?? 'N/A' }} min | {{ ucfirst($course->level) }}</p></div>
                        <div class="col-12"><label class="small text-muted">Description</label><p>{{ $course->description ?? 'N/A' }}</p></div>
                        <div class="col-12"><label class="small text-muted">Learning Objectives</label><p>{{ $course->learning_objectives ?? 'N/A' }}</p></div>
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-collection me-2"></i>Modules & Lessons ({{ $course->modules->count() }} modules, {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} lessons)</h6>
                    <div class="accordion" id="reviewModules">
                        @foreach($course->modules as $mIndex => $module)
                            <div class="accordion-item"><h2 class="accordion-header">
                                <button class="accordion-button {{ $mIndex > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#rm{{ $module->module_id }}">
                                    {{ $module->title }} <span class="badge bg-light text-dark ms-2">{{ $module->lessons->count() }} lessons</span>
                                </button></h2>
                                <div id="rm{{ $module->module_id }}" class="accordion-collapse collapse {{ $mIndex === 0 ? 'show' : '' }}" data-bs-parent="#reviewModules">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled mb-0">
                                            @foreach($module->lessons as $lesson)
                                                <li class="py-1 border-bottom"><i class="bi bi-play-circle text-primary me-2"></i>{{ $lesson->title }} <small class="text-muted">({{ $lesson->duration }} min)</small></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quizzes Review -->
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Quizzes ({{ $course->quizzes->count() }})</h6>
                    @foreach($course->quizzes as $quiz)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-medium">{{ $quiz->title }}</h6>
                                <span class="badge bg-primary">{{ $quiz->questions->count() }} questions</span>
                            </div>
                            <small class="text-muted">Pass: {{ $quiz->passing_marks }}/{{ $quiz->total_marks }} | Duration: {{ $quiz->duration_minutes }} min</small>
                            <div class="mt-2 small">
                                @foreach($quiz->questions as $q)
                                    <div class="mb-1 p-1 rounded {{ $q->question_type !== 'mcq' ? 'bg-info bg-opacity-10' : '' }}">
                                        <span class="badge bg-{{ $q->difficulty === 'hard' ? 'danger' : ($q->difficulty === 'medium' ? 'warning' : 'success') }} me-1">{{ $q->difficulty }}</span>
                                        <span class="badge bg-secondary me-1">{{ str_replace('_', ' ', $q->question_type) }}</span>
                                        {{ Str::limit($q->question_text, 60) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Validation Results -->
            <div class="card stat-card mb-4">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2"></i>Validation Score</h6>
                    <div class="display-3 fw-bold text-{{ $validation['score'] >= 80 ? 'success' : ($validation['score'] >= 50 ? 'warning' : 'danger') }}">{{ $validation['score'] }}%</div>
                    <div class="progress mt-2" style="height:10px;"><div class="progress-bar bg-{{ $validation['score'] >= 80 ? 'success' : ($validation['score'] >= 50 ? 'warning' : 'danger') }}" style="width: {{ $validation['score'] }}%"></div></div>
                    @if(!empty($validation['issues']))<div class="mt-2"><div class="text-danger small"><i class="bi bi-x-circle"></i> {{ count($validation['issues']) }} issues</div></div>@endif
                    @if(!empty($validation['warnings']))<div class="mt-1"><div class="text-warning small"><i class="bi bi-exclamation-triangle"></i> {{ count($validation['warnings']) }} warnings</div></div>@endif
                </div>
            </div>

            <!-- Validation Details -->
            @if(!empty($validation['issues']) || !empty($validation['warnings']))
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2"></i>Validation Details</h6>
                    @foreach($validation['issues'] ?? [] as $issue)
                        <div class="alert alert-danger py-2 small mb-2"><i class="bi bi-x-circle me-1"></i> {{ $issue['message'] }}</div>
                    @endforeach
                    @foreach($validation['warnings'] ?? [] as $warning)
                        <div class="alert alert-warning py-2 small mb-2"><i class="bi bi-exclamation-triangle me-1"></i> {{ $warning['message'] }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Review Actions -->
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-check2-circle me-2"></i>Review Actions</h6>
                    <form method="POST" action="{{ route('approval.submit-review', $course) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Review Type</label>
                            <select name="review_type" class="form-select" required>
                                <option value="instructor" {{ auth()->user()->isInstructor() ? 'selected' : '' }}>Instructor Review</option>
                                <option value="coordinator" {{ auth()->user()->isAdmin() ? '' : '' }}>Coordinator Review</option>
                                <option value="admin" {{ auth()->user()->isAdmin() ? 'selected' : '' }}>Admin Approval</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Comments</label><textarea name="comments" class="form-control" rows="3" placeholder="Provide feedback..."></textarea></div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success"><i class="bi bi-check-lg"></i> Approve</button>
                            <button type="submit" name="action" value="revision" class="btn btn-warning"><i class="bi bi-arrow-repeat"></i> Request Revision</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger"><i class="bi bi-x-lg"></i> Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
