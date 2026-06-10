@extends('layouts.app')

@section('title', 'Take Final Exam - ' . $course->title)

@section('content')
<div class="fade-in">
    <div class="card stat-card">
        <div class="card-header bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 text-danger"><i class="bi bi-pencil-square me-2"></i>{{ $exam->title }}</h5>
                    <p class="text-muted mb-0">{{ $course->title }}</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Questions: {{ $exam->questions->count() }} | Total Marks: {{ $exam->total_marks }} | Pass: {{ $exam->passing_marks }}</small>
                    @if($exam->duration_minutes)
                        <div><small class="text-muted"><i class="bi bi-clock"></i> Time limit: {{ $exam->duration_minutes }} min | Attempt {{ $attemptsUsed + 1 }} of {{ $exam->attempts_allowed }}</small></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('final-exams.submit', [$course, $exam]) }}" id="examForm" onsubmit="return confirm('Are you sure you want to submit your final exam? This action cannot be undone.')">
                @csrf
                @foreach($exam->questions as $index => $question)
                    <div class="border rounded p-4 mb-4 question-card">
                        <h6 class="fw-bold mb-3">
                            <span class="badge bg-danger me-2">Q{{ $index + 1 }}</span>
                            {{ $question->question_text }}
                        </h6>
                        <div class="row g-3">
                            @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
                                <div class="col-md-6">
                                    <div class="form-check border rounded p-3 option-card" onclick="selectOption(this, '{{ $question->question_id }}', '{{ $key }}')">
                                        <input class="form-check-input d-none" type="radio" name="answers[{{ $question->question_id }}]" value="{{ $key }}" id="q{{ $question->question_id }}_{{ $key }}">
                                        <label class="form-check-label w-100" for="q{{ $question->question_id }}_{{ $key }}">
                                            <span class="badge bg-secondary me-2">{{ $label }}</span>
                                            {{ $question['option_' . $key] }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted mb-0"><i class="bi bi-info-circle"></i> Review all answers carefully before submitting.</p>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="bi bi-check-lg"></i> Submit Final Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .option-card { cursor: pointer; transition: all 0.2s; }
    .option-card:hover { border-color: #dc3545; background: rgba(220,53,69,0.05); }
    .option-card.selected { border-color: #dc3545; background: rgba(220,53,69,0.1); }
    .question-card { transition: all 0.2s; }
    .question-card:hover { border-color: #dc3545; }
</style>

@push('scripts')
<script>
function selectOption(element, questionId, value) {
    document.querySelectorAll(`input[name="answers[${questionId}]"]`).forEach(el => {
        el.checked = false;
        el.closest('.option-card')?.classList.remove('selected');
    });
    element.classList.add('selected');
    document.getElementById(`q${questionId}_${value}`).checked = true;
}

// Timer functionality
@if($exam->duration_minutes)
let timeLeft = {{ $exam->duration_minutes * 60 }};
const timerDisplay = document.createElement('div');
timerDisplay.className = 'alert alert-danger text-center mb-0 py-2';
timerDisplay.id = 'examTimer';

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerDisplay.innerHTML = `<i class="bi bi-clock-fill me-2"></i> Time Remaining: <strong>${minutes}:${seconds.toString().padStart(2, '0')}</strong>`;
    if (timeLeft <= 300) { // 5 minutes warning
        timerDisplay.className = 'alert alert-danger text-center mb-0 py-2';
    }
    if (timeLeft <= 0) {
        document.getElementById('examForm').submit();
    }
    timeLeft--;
}

document.querySelector('.card-header').appendChild(timerDisplay);
setInterval(updateTimer, 1000);
updateTimer();
@endif
</script>
@endpush
@endsection
