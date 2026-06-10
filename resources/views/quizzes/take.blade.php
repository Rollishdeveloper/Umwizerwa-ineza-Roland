@extends('layouts.app')

@section('title', 'Take Quiz - ' . $quiz->title)

@section('content')
<div class="content-transition">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Quiz Header --}}
            <div class="glass-card p-4 mb-4 animate-fade-in-up">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2" style="font-size: 0.7rem;">
                            <i class="bi bi-pencil-square me-1"></i> Quiz
                        </span>
                        <h4 class="fw-bold mb-1">{{ $quiz->title }}</h4>
                        <p class="text-muted small mb-0">{{ $quiz->description }}</p>
                    </div>
                    <div class="text-center">
                        <div class="quiz-timer" id="quizTimer">
                            <i class="bi bi-clock-history me-1"></i>
                            <span id="timerDisplay">{{ $quiz->duration_minutes }}:00</span>
                        </div>
                        <small class="text-muted">{{ $quiz->questions->count() }} questions</small>
                    </div>
                </div>
                {{-- Progress bar --}}
                <div class="mt-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Progress</span>
                        <span class="fw-medium" id="progressText">0 / {{ $quiz->questions->count() }}</span>
                    </div>
                    <div class="xp-bar-container" style="height: 6px;">
                        <div class="xp-bar-fill" id="progressBar" style="width: 0%;"></div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('quizzes.submit', ['course' => $course ?? $quiz->course, 'quiz' => $quiz]) }}" id="quizForm">
                @csrf

                @foreach($quiz->questions as $index => $question)
                    <div class="glass-card p-4 mb-3 question-card animate-fade-in-up" 
                         id="question-{{ $index }}" 
                         style="animation-delay: {{ $index * 0.1 }}s; {{ $index > 0 ? 'display: none;' : '' }}">
                        
                        {{-- Question header --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary" style="font-size: 0.65rem;">
                                Question {{ $index + 1 }} of {{ $quiz->questions->count() }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $question->difficulty === 'easy' ? 'success' : ($question->difficulty === 'hard' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $question->difficulty === 'easy' ? 'success' : ($question->difficulty === 'hard' ? 'danger' : 'warning') }}" 
                                 style="font-size: 0.65rem;">
                                {{ ucfirst($question->difficulty ?? 'medium') }} • {{ $question->marks ?? 10 }} pts
                            </span>
                        </div>

                        {{-- Question text --}}
                        <h5 class="fw-bold mb-4">{{ $question->question_text }}</h5>

                        {{-- Options --}}
                        @if($question->question_type === 'mcq' || $question->question_type === 'true_false')
                            @php
                                $options = [];
                                foreach (['a' => $question->option_a, 'b' => $question->option_b, 'c' => $question->option_c, 'd' => $question->option_d] as $key => $val) {
                                    if (!empty($val)) $options[$key] = $val;
                                }
                            @endphp
                            <div class="d-flex flex-column gap-2">
                                @foreach($options as $key => $val)
                                    <label class="quiz-option d-flex align-items-center gap-3" onclick="selectOption(this, '{{ $question->question_id }}', '{{ $key }}')">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width: 36px; height: 36px; background: var(--border-color); font-size: 0.85rem;">
                                            {{ strtoupper($key) }}
                                        </div>
                                        <span>{{ $val }}</span>
                                        <input type="radio" name="answers[{{ $question->question_id }}]" value="{{ $key }}" 
                                               class="d-none" onchange="this.closest('.quiz-option').classList.add('selected')">
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->question_type === 'fill_blank')
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-lg" 
                                       name="answers[{{ $question->question_id }}]" 
                                       placeholder="Type your answer..."
                                       style="border: 2px solid var(--border-color); border-radius: var(--radius-md);">
                            </div>
                        @else
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" 
                                          name="answers[{{ $question->question_id }}]" 
                                          placeholder="Write your answer..."
                                          style="border: 2px solid var(--border-color); border-radius: var(--radius-md);"></textarea>
                            </div>
                        @endif

                        {{-- Navigation buttons --}}
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top border-light">
                            <button type="button" class="btn btn-glass" onclick="navigateQuestion(-1)" 
                                    id="prevBtn" {{ $index === 0 ? 'disabled' : '' }}>
                                <i class="bi bi-arrow-left me-1"></i> Previous
                            </button>
                            @if($loop->last)
                                <button type="submit" class="btn btn-premium" id="submitQuiz">
                                    <i class="bi bi-check-lg me-1"></i> Submit Quiz
                                </button>
                            @else
                                <button type="button" class="btn btn-premium" onclick="navigateQuestion(1)">
                                    Next <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
</div>

{{-- Confetti Container --}}
<div class="confetti-container" id="confettiContainer"></div>

@endsection

@push('scripts')
<script>
    let currentQuestion = 0;
    const totalQuestions = {{ $quiz->questions->count() }};
    const durationMinutes = {{ $quiz->duration_minutes ?? 10 }};
    let timeLeft = durationMinutes * 60;

    // Timer
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timerDisplay').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        
        const timerEl = document.getElementById('quizTimer');
        if (timeLeft <= 60) {
            timerEl.classList.add('danger');
        } else if (timeLeft <= 180) {
            timerEl.classList.add('warning');
        }
        
        if (timeLeft <= 0) {
            document.getElementById('quizForm').submit();
        }
        timeLeft--;
    }
    setInterval(updateTimer, 1000);

    // Navigation
    function navigateQuestion(direction) {
        document.getElementById('question-' + currentQuestion).style.display = 'none';
        currentQuestion += direction;
        document.getElementById('question-' + currentQuestion).style.display = 'block';
        document.getElementById('prevBtn').disabled = currentQuestion === 0;
        updateProgress();
    }

    function selectOption(el, questionId, value) {
        const parent = el.closest('.d-flex.flex-column');
        parent.querySelectorAll('.quiz-option').forEach(opt => {
            opt.classList.remove('selected');
            opt.querySelector('input[type="radio"]').checked = false;
        });
        el.classList.add('selected');
        el.querySelector('input[type="radio"]').checked = true;
    }

    function updateProgress() {
        const radios = document.querySelectorAll('input[type="radio"]:checked').length;
        const textInputs = document.querySelectorAll('input[type="text"]');
        const textareas = document.querySelectorAll('textarea');
        let filled = radios;
        textInputs.forEach(el => { if(el.value.trim().length > 0) filled++; });
        textareas.forEach(el => { if(el.value.trim().length > 0) filled++; });
        document.getElementById('progressText').textContent = filled + ' / ' + totalQuestions;
        document.getElementById('progressBar').style.width = (filled / totalQuestions * 100) + '%';
    }

    // Listen for input changes
    document.querySelectorAll('input, textarea').forEach(el => {
        el.addEventListener('input', updateProgress);
        el.addEventListener('change', updateProgress);
    });

    // Confetti on submit
    document.getElementById('submitQuiz')?.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to submit your quiz? You have ' + 
            Math.floor(timeLeft / 60) + ' minutes and ' + (timeLeft % 60) + ' seconds remaining.')) {
            e.preventDefault();
            return;
        }
        // Simple confetti
        const container = document.getElementById('confettiContainer');
        const colors = ['#4F46E5', '#7C3AED', '#F59E0B', '#10B981', '#EC4899', '#06B6D4'];
        for (let i = 0; i < 50; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.left = Math.random() * 100 + '%';
            piece.style.background = colors[Math.floor(Math.random() * colors.length)];
            piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            piece.style.width = (Math.random() * 8 + 4) + 'px';
            piece.style.height = (Math.random() * 8 + 4) + 'px';
            piece.style.animationDelay = Math.random() * 0.5 + 's';
            piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
            container.appendChild(piece);
        }
        setTimeout(() => container.innerHTML = '', 4000);
    });

    // Initialize progress
    updateProgress();
</script>
@endpush
