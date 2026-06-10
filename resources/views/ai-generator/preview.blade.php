@extends('layouts.app')
@section('title', 'Review Generated Course')
@section('content')
<div class="fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-eye text-primary me-2"></i>Review & Customize Course</h4>
            <p class="text-muted mb-0">Based on: <strong>{{ $material->original_filename }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ai-generator.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('ai-generator.generate', $material) }}">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $selectedCategory ?? '' }}">

                        <!-- Course Info -->
                        <div class="mb-4 p-3 bg-light rounded-3">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-medium small">Course Title</label>
                                    <input type="text" name="title" class="form-control form-control-lg fw-bold" value="{{ $structure['title'] }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">Difficulty Level</label>
                                    <select class="form-select" name="difficulty">
                                        <option value="beginner" {{ $structure['difficulty'] === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ $structure['difficulty'] === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ $structure['difficulty'] === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="form-label fw-medium small">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ $structure['description'] }}</textarea>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <span class="badge bg-info fs-6"><i class="bi bi-clock"></i> {{ $structure['duration'] }} min</span>
                                <span class="badge bg-primary fs-6"><i class="bi bi-collection"></i> {{ count($structure['modules']) }} modules</span>
                                <span class="badge bg-success fs-6"><i class="bi bi-lightbulb"></i> {{ count($structure['concepts_detected']) }} concepts</span>
                                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-robot"></i> {{ $analysis['confidence'] }}% confidence</span>
                            </div>
                        </div>

                        <!-- Modules & Lessons Accordion -->
                        <h6 class="fw-bold mb-3"><i class="bi bi-collection me-2"></i>Course Modules</h6>

                        <div id="moduleSortable" class="accordion" id="previewAccordion">
                            @foreach($structure['modules'] as $index => $module)
                                <div class="accordion-item module-item mb-2" data-module-index="{{ $index }}">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#module{{ $index }}">
                                            <div class="d-flex align-items-center gap-2 w-100">
                                                <i class="bi bi-grip-vertical text-muted handle" style="cursor: grab;"></i>
                                                <span class="fw-medium">Module {{ $module['position'] }}: <span class="module-title-text">{{ $module['title'] }}</span></span>
                                                <span class="badge bg-light text-dark ms-auto me-3">{{ count($module['lessons']) }} lessons | {{ $module['num_quiz_questions'] }} quiz Qs</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="module{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#previewAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-medium">Module Title</label>
                                                <input type="text" class="form-control form-control-sm module-title-input" value="{{ $module['title'] }}" 
                                                       onchange="updateModuleTitle(this, {{ $index }})">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-medium">Description</label>
                                                <textarea class="form-control form-control-sm" rows="2">{{ $module['description'] }}</textarea>
                                            </div>

                                            <h6 class="fw-medium small mb-2"><i class="bi bi-play-circle me-1"></i>Lessons</h6>
                                            <div class="lesson-list">
                                                @foreach($module['lessons'] as $li => $lesson)
                                                    <div class="lesson-item border rounded p-2 mb-2 d-flex align-items-center gap-2">
                                                        <i class="bi bi-grip-vertical text-muted" style="cursor: grab;"></i>
                                                        <i class="bi bi-play-circle text-primary"></i>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $lesson['title'] }}" style="max-width: 350px;">
                                                        <input type="number" class="form-control form-control-sm" value="{{ $lesson['duration'] }}" min="5" max="120" style="width: 70px;" title="Duration (min)">
                                                        <small class="text-muted">min</small>
                                                        <div class="form-check form-switch ms-auto">
                                                            <input class="form-check-input" type="checkbox" {{ $lesson['has_video'] ? 'checked' : '' }}>
                                                            <label class="form-check-label small"><i class="bi bi-camera-video"></i></label>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" {{ $lesson['has_exercises'] ? 'checked' : '' }}>
                                                            <label class="form-check-label small"><i class="bi bi-tools"></i></label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="d-flex gap-2 mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLesson(this)"><i class="bi bi-plus"></i> Add Lesson</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeModule(this)"><i class="bi bi-trash"></i> Remove Module</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-3" onclick="addModule()">
                            <i class="bi bi-plus-circle"></i> Add Module
                        </button>

                        <!-- Generation Options -->
                        <div class="mt-4 p-3 border rounded-3">
                            <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Generation Options</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="optVideo" checked>
                                        <label class="form-check-label" for="optVideo">
                                            <i class="bi bi-camera-video me-1"></i> Video Scripts
                                            <small class="d-block text-muted">Generate teaching scripts for each lesson</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="optPresentation" checked>
                                        <label class="form-check-label" for="optPresentation">
                                            <i class="bi bi-easel me-1"></i> Presentations
                                            <small class="d-block text-muted">Generate slide content per lesson</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="optCertificate">
                                        <label class="form-check-label" for="optCertificate">
                                            <i class="bi bi-award me-1"></i> Auto Certificate
                                            <small class="d-block text-muted">Auto-issue certificates on completion</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Section -->
                        <div class="mt-4 p-4 border rounded-3 bg-success bg-opacity-10 text-center">
                            <h5 class="fw-bold text-success"><i class="bi bi-check-circle me-2"></i>Ready to Generate?</h5>
                            <p class="text-muted small mb-3">After generating, the course will be created with all content and you can continue editing in the course editor.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()"><i class="bi bi-save me-1"></i> Save as Draft</button>
                                <button type="submit" class="btn btn-success btn-lg px-5"><i class="bi bi-rocket-takeoff me-2"></i> Generate Course</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Analysis Stats -->
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart text-primary me-2"></i>Content Analysis</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">AI Confidence</span><span class="fw-medium">{{ $analysis['confidence'] }}%</span></div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $analysis['confidence'] >= 80 ? 'success' : ($analysis['confidence'] >= 50 ? 'warning' : 'danger') }}" 
                                 style="width: {{ $analysis['confidence'] }}%"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Words Extracted</span><span class="fw-medium">{{ number_format($analysis['total_words']) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Chapters Detected</span><span class="fw-medium">{{ count($analysis['chapters']) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Key Concepts</span><span class="fw-medium">{{ count($analysis['key_concepts']) }}</span></div>
                    <hr>
                    <h6 class="fw-bold small mb-2">Detected Concepts</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($analysis['key_concepts'] as $concept)
                            <span class="badge bg-light text-dark border">{{ $concept }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- What Will Be Generated -->
            <div class="card stat-card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-list-check text-info me-2"></i>Items to Generate</h6>
                    <ul class="small mb-0 list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> {{ count($structure['modules']) }} Modules</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> {{ array_sum(array_map(fn($m) => count($m['lessons'] ?? []), $structure['modules'])) }} Lessons</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> {{ count($structure['modules']) }} Module Quizzes</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> 3 Practice Assignments</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> 1 Final Examination</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i> Question Bank Entries</li>
                    </ul>
                </div>
            </div>

            <!-- Edit Tips -->
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil text-warning me-2"></i>Tips Before Generating</h6>
                    <ul class="small text-muted mb-0">
                        <li class="mb-1">Adjust module titles and lesson names</li>
                        <li class="mb-1">Reorder modules by dragging the grip icon</li>
                        <li class="mb-1">Add or remove lessons as needed</li>
                        <li class="mb-1">Set appropriate difficulty level</li>
                        <li class="mb-1">Toggle video scripts & presentations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.module-item .handle:hover { cursor: grab; }
.lesson-item:hover { background: rgba(102,126,234,0.03); }
</style>
@endsection

@push('scripts')
<script>
function addLesson(btn) {
    const lessonList = btn.closest('.accordion-body').querySelector('.lesson-list');
    const item = document.createElement('div');
    item.className = 'lesson-item border rounded p-2 mb-2 d-flex align-items-center gap-2';
    item.innerHTML = `
        <i class="bi bi-grip-vertical text-muted" style="cursor: grab;"></i>
        <i class="bi bi-play-circle text-primary"></i>
        <input type="text" class="form-control form-control-sm" value="New Lesson" style="max-width: 350px;">
        <input type="number" class="form-control form-control-sm" value="15" min="5" max="120" style="width: 70px;">
        <small class="text-muted">min</small>
        <div class="form-check form-switch ms-auto">
            <input class="form-check-input" type="checkbox">
            <label class="form-check-label small"><i class="bi bi-camera-video"></i></label>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
    `;
    lessonList.appendChild(item);
}

function removeModule(btn) {
    if (confirm('Remove this module and all its lessons?')) {
        btn.closest('.module-item').remove();
    }
}

function addModule() {
    const container = document.getElementById('moduleSortable');
    const index = container.children.length;
    const div = document.createElement('div');
    div.className = 'accordion-item module-item mb-2';
    div.innerHTML = `
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#module${index}">
                <div class="d-flex align-items-center gap-2 w-100">
                    <i class="bi bi-grip-vertical text-muted handle" style="cursor: grab;"></i>
                    <span class="fw-medium">Module ${index + 1}: <span class="module-title-text">New Module</span></span>
                    <span class="badge bg-light text-dark ms-auto me-3">1 lesson</span>
                </div>
            </button>
        </h2>
        <div id="module${index}" class="accordion-collapse collapse" data-bs-parent="#previewAccordion">
            <div class="accordion-body">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Module Title</label>
                    <input type="text" class="form-control form-control-sm module-title-input" value="New Module">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Description</label>
                    <textarea class="form-control form-control-sm" rows="2">Description for this module</textarea>
                </div>
                <h6 class="fw-medium small mb-2"><i class="bi bi-play-circle me-1"></i>Lessons</h6>
                <div class="lesson-list">
                    <div class="lesson-item border rounded p-2 mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-play-circle text-primary"></i>
                        <input type="text" class="form-control form-control-sm" value="Introduction" style="max-width: 350px;">
                        <input type="number" class="form-control form-control-sm" value="15" style="width: 70px;">
                        <small class="text-muted">min</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addLesson(this)"><i class="bi bi-plus"></i> Add Lesson</button>
            </div>
        </div>
    `;
    container.appendChild(div);
}

function updateModuleTitle(input, index) {
    const textSpan = input.closest('.accordion-body').parentElement.querySelector('.module-title-text');
    if (textSpan) textSpan.textContent = input.value;
}

function saveAsDraft() {
    // Create a hidden form copy that sets status=draft before submitting
    const form = document.querySelector('form');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'status';
    input.value = 'draft';
    form.appendChild(input);
    form.submit();
}
</script>
@endpush
