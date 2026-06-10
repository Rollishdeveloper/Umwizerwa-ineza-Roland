@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="content-transition position-relative" style="min-height: 80vh;">
    {{-- Course Header --}}
    <div class="glass-card p-4 mb-4 animate-fade-in-up">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex gap-2 mb-2">
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">
                        {{ ucfirst($course->level ?? 'beginner') }}
                    </span>
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" style="font-size: 0.7rem;">
                        <i class="bi bi-clock me-1"></i>{{ $course->duration ?? 'N/A' }} min
                    </span>
                    @if($course->category)
                        <span class="badge rounded-pill bg-info bg-opacity-10 text-info" style="font-size: 0.7rem;">
                            {{ $course->category->name }}
                        </span>
                    @endif
                </div>
                <h2 class="fw-bold mb-2">{{ $course->title }}</h2>
                <p class="text-muted mb-3">{{ Str::limit($course->description, 200) }}</p>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    @if($course->instructor && $course->instructor->user)
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width: 36px; height: 36px; background: var(--primary-gradient); font-size: 0.8rem;">
                                {{ strtoupper(substr($course->instructor->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <small class="fw-medium d-block">{{ $course->instructor->user->name }}</small>
                                <small class="text-muted">Instructor</small>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-star-fill" style="color: #F59E0B;"></i>
                        <span class="fw-medium">4.8</span>
                        <span class="text-muted">(128 reviews)</span>
                    </div>
                    @if($course->modules_count ?? $course->modules->count())
                        <small class="text-muted">
                            <i class="bi bi-collection me-1"></i>{{ $course->modules->count() }} modules
                        </small>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="d-inline-block p-4 rounded-4" style="background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.1)); border: 1px solid rgba(255,255,255,0.05);">
                    @php $completionPct = $enrollment ? ($enrollment->completion_percentage ?? 0) : 0; @endphp
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <defs>
                            <linearGradient id="courseProg" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#4F46E5"/>
                                <stop offset="100%" stop-color="#7C3AED"/>
                            </linearGradient>
                        </defs>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="var(--border-color)" stroke-width="8"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="url(#courseProg)" stroke-width="8"
                                stroke-dasharray="339.292" stroke-dashoffset="{{ 339.292 - (339.292 * $completionPct / 100) }}"
                                stroke-linecap="round" transform="rotate(-90 60 60)"
                                style="transition: stroke-dashoffset 1.5s ease;"/>
                        <text x="60" y="55" text-anchor="middle" fill="currentColor" font-size="24" font-weight="700">{{ $completionPct }}%</text>
                        <text x="60" y="75" text-anchor="middle" fill="var(--text-muted)" font-size="10">complete</text>
                    </svg>
                </div>
                @auth
                    @if(auth()->user()->isStudent() && !$enrollment)
                        <form method="POST" action="{{ route('enrollments.store', $course) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-premium w-100">
                                <i class="bi bi-plus-circle me-1"></i> Enroll Now
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Learning Journey Roadmap --}}
            <div class="glass-card p-4 mb-4 animate-fade-in-up delay-1">
                <h5 class="fw-bold mb-4"><i class="bi bi-signpost-2 me-2" style="color: #4F46E5;"></i>Learning Journey</h5>
                <div class="roadmap-container">
                    <div class="roadmap-line"></div>
                    @forelse($course->modules as $module)
                        <div class="roadmap-node {{ $loop->first ? 'completed' : '' }}">
                            <div class="glass-card p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-1" style="font-size: 0.65rem;">
                                            Module {{ $module->position }}
                                        </span>
                                        <h6 class="fw-bold mb-1">{{ $module->title }}</h6>
                                        <p class="text-muted small mb-2">{{ Str::limit($module->description, 100) }}</p>
                                        <div class="d-flex gap-2">
                                            <small class="text-muted"><i class="bi bi-collection me-1"></i>{{ $module->lessons->count() }} lessons</small>
                                        </div>
                                    </div>
                                    @if($loop->first)
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check-lg"></i> Current
                                        </span>
                                    @elseif($loop->remaining == 0)
                                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">
                                            <i class="bi bi-lock"></i> Locked
                                        </span>
                                    @endif
                                </div>
                                @if($module->lessons->count() > 0)
                                    <div class="mt-2 pt-2 border-top border-light">
                                        @foreach($module->lessons as $lesson)
                                            <a href="{{ route('lessons.show', $lesson) }}" class="d-flex align-items-center gap-2 py-1 text-decoration-none">
                                                <i class="bi bi-play-circle-fill" style="color: #4F46E5; font-size: 0.8rem;"></i>
                                                <small class="text-muted">{{ $lesson->title }}</small>
                                                <small class="text-muted ms-auto">{{ $lesson->duration }} min</small>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-journal fs-2 d-block mb-2"></i>
                            <p>No modules yet.</p>
                        </div>
                    @endforelse
                    <div class="roadmap-node">
                        <div class="glass-card p-3 text-center">
                            <i class="bi bi-award fs-3 d-block mb-2" style="color: #F59E0B;"></i>
                            <h6 class="fw-bold mb-1">Final Examination</h6>
                            <p class="text-muted small mb-0">Complete all modules to unlock</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-3 animate-fade-in-up delay-2">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2" style="color: #4F46E5;"></i>Course Details</h5>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Duration</small>
                        <small class="fw-medium">{{ $course->duration ?? 'N/A' }} minutes</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Level</small>
                        <small class="fw-medium">{{ ucfirst($course->level ?? 'beginner') }}</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Category</small>
                        <small class="fw-medium">{{ $course->category->name ?? 'N/A' }}</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Modules</small>
                        <small class="fw-medium">{{ $course->modules->count() }}</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Quizzes</small>
                        <small class="fw-medium">{{ $course->quizzes->count() }}</small>
                    </div>
                </div>
            </div>

            @if($course->quizzes->count() > 0)
                <div class="glass-card p-4 mb-3 animate-fade-in-up delay-3">
                    <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2" style="color: #F59E0B;"></i>Quizzes</h5>
                    @foreach($course->quizzes as $quiz)
                        <a href="{{ route('quizzes.show', ['course' => $course, 'quiz' => $quiz]) }}" 
                           class="d-flex justify-content-between align-items-center py-2 text-decoration-none border-bottom border-light">
                            <div>
                                <small class="fw-medium d-block">{{ $quiz->title }}</small>
                                <small class="text-muted">{{ $quiz->questions_count ?? 0 }} questions</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    @endforeach
                </div>
            @endif

            @if($course->assignments->count() > 0)
                <div class="glass-card p-4 mb-3 animate-fade-in-up delay-3">
                    <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2" style="color: #10B981;"></i>Assignments</h5>
                    @foreach($course->assignments as $assignment)
                        <div class="py-2 border-bottom border-light">
                            <small class="fw-medium d-block">{{ $assignment->title }}</small>
                            <small class="text-muted">{{ $assignment->total_marks }} marks</small>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($course->learning_objectives)
                <div class="glass-card p-4 animate-fade-in-up delay-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-bullseye me-2" style="color: #EC4899;"></i>What You'll Learn</h5>
                    @foreach(explode("\n", $course->learning_objectives) as $obj)
                        @if(trim($obj))
                            <div class="d-flex gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-success flex-shrink-0" style="font-size: 0.8rem; margin-top: 3px;"></i>
                                <small class="text-muted">{{ trim($obj) }}</small>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Floating AI Assistant --}}
<div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1050;">
    <div id="aiAssistantPanel" class="glass-card mb-3 p-3" style="width: 320px; display: none;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold"><i class="bi bi-robot me-1" style="color: #4F46E5;"></i> AI Tutor</span>
            <button class="btn btn-sm btn-glass" onclick="toggleAIAssistant()"><i class="bi bi-x"></i></button>
        </div>
        <div id="aiChat" style="height: 200px; overflow-y: auto;" class="mb-2 small">
            <div class="d-flex gap-2 mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 28px; height: 28px; background: var(--primary-gradient); font-size: 0.65rem;">
                    <i class="bi bi-robot text-white"></i>
                </div>
                <div class="bg-light rounded-3 p-2" style="background: var(--surface-secondary) !important;">
                    Hi! I can help explain concepts, summarize lessons, or answer questions about this course.
                </div>
            </div>
        </div>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="aiChatInput" placeholder="Ask me anything..."
                   onkeypress="if(event.key==='Enter') sendAIMessage()">
            <button class="btn btn-premium" onclick="sendAIMessage()"><i class="bi bi-send"></i></button>
        </div>
    </div>
    <div id="aiFab" onclick="toggleAIAssistant()" 
         style="width: 56px; height: 56px; border-radius: 50%; background: var(--primary-gradient); cursor: pointer; box-shadow: 0 4px 20px rgba(79,70,229,0.4); display: flex; align-items: center; justify-content: center; margin-left: auto;">
        <i class="bi bi-robot text-white fs-4"></i>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let aiOpen = false;
    function toggleAIAssistant() {
        aiOpen = !aiOpen;
        document.getElementById('aiAssistantPanel').style.display = aiOpen ? 'block' : 'none';
    }
    function sendAIMessage() {
        const input = document.getElementById('aiChatInput');
        const msg = input.value.trim();
        if (!msg) return;
        const chat = document.getElementById('aiChat');
        chat.innerHTML += '<div class="d-flex gap-2 mb-2 justify-content-end"><div class="rounded-3 p-2" style="background: var(--primary-gradient); color: #fff;">' + msg + '</div></div>';
        input.value = '';
        setTimeout(() => {
            chat.innerHTML += '<div class="d-flex gap-2 mb-2"><div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background: var(--primary-gradient); font-size: 0.65rem;"><i class="bi bi-robot text-white"></i></div><div class="bg-light rounded-3 p-2" style="background: var(--surface-secondary) !important;">Great question! I\'m a demo AI assistant. In production, I would connect to OpenAI to provide detailed explanations about ' + document.title.replace(' - Learning Management System', '') + '.</div></div>';
            chat.scrollTop = chat.scrollHeight;
        }, 800);
        chat.scrollTop = chat.scrollHeight;
    }
</script>
@endpush
