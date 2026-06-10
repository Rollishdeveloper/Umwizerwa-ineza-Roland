@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="fade-in">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $quiz->title }}</h6>
                    <div class="mb-2"><small class="text-muted">Description:</small><p>{{ $quiz->description ?? 'No description' }}</p></div>
                    <div class="d-flex justify-content-between mb-2"><span>Total Marks:</span><span class="fw-bold">{{ $quiz->total_marks }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Passing Marks:</span><span class="fw-bold text-success">{{ $quiz->passing_marks }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Duration:</span><span class="fw-bold">{{ $quiz->duration_minutes ?? 'N/A' }} min</span></div>
                    <div class="d-flex justify-content-between mb-3"><span>Questions:</span><span class="fw-bold">{{ $quiz->questions->count() }}</span></div>
                    <a href="{{ route('quizzes.take', [$course, $quiz]) }}" class="btn btn-primary w-100"><i class="bi bi-play"></i> Take Quiz</a>

                    @if(auth()->user()->isInstructor())
                    <div class="mt-3">
                        <a href="{{ route('question-bank.generate', $course) }}" class="btn btn-outline-info btn-sm w-100">
                            <i class="bi bi-database"></i> Generate Questions from Bank
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card stat-card">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Questions ({{ $quiz->questions->count() }})</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal"><i class="bi bi-plus"></i> Add</button>
                </div>
                <div class="card-body">
                    @forelse($quiz->questions as $index => $question)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-medium mb-1">Q{{ $index + 1 }}. {{ $question->question_text }}</h6>
                                    <div class="d-flex gap-1 mb-2">
                                        <span class="badge bg-{{ $question->question_type === 'true_false' ? 'info' : ($question->question_type === 'mcq' ? 'primary' : 'secondary') }}" style="font-size:0.65rem;">
                                            {{ $question->question_type === 'true_false' ? 'True/False' : ($question->question_type === 'mcq' ? 'MCQ' : str_replace('_', ' ', $question->question_type)) }}
                                        </span>
                                        @if($question->difficulty)
                                            <span class="badge bg-{{ $question->difficulty === 'hard' ? 'danger' : ($question->difficulty === 'medium' ? 'warning' : 'success') }}" style="font-size:0.65rem;">{{ ucfirst($question->difficulty) }}</span>
                                        @endif
                                        @if($question->marks)
                                            <span class="badge bg-light text-dark" style="font-size:0.65rem;">{{ $question->marks }} pts</span>
                                        @endif
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete?')) document.getElementById('delete-q-{{ $question->question_id }}').submit()">
                                    <i class="bi bi-trash"></i></button>
                                <form id="delete-q-{{ $question->question_id }}" action="{{ route('quizzes.questions.destroy', [$course, $quiz, $question]) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </div>
                            <div class="row g-2">
                                @if($question->question_type === 'true_false')
                                    @foreach(['a','b'] as $option)
                                        <div class="col-6">
                                            <div class="border rounded p-2 small {{ $question->correct_answer === $option ? 'bg-success bg-opacity-10 border-success' : '' }}">
                                                <span class="badge bg-{{ $question->correct_answer === $option ? 'success' : 'secondary' }} me-1">
                                                    <i class="bi bi-{{ $option === 'a' ? 'check-lg' : 'x-lg' }}"></i>
                                                </span>
                                                {{ $option === 'a' ? 'True' : 'False' }}
                                                @if($question->correct_answer === $option)
                                                    <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach(['a','b','c','d'] as $option)
                                        @if($question['option_' . $option])
                                            <div class="col-6">
                                                <div class="border rounded p-2 small {{ $question->correct_answer === $option ? 'bg-success bg-opacity-10 border-success' : '' }}">
                                                    <span class="badge bg-{{ $question->correct_answer === $option ? 'success' : 'secondary' }} me-1">{{ strtoupper($option) }}</span>
                                                    {{ $question['option_' . $option] }}
                                                    @if($question->correct_answer === $option)
                                                        <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            @if($question->explanation)
                                <div class="mt-2 p-2 bg-info bg-opacity-10 rounded small">
                                    <i class="bi bi-info-circle text-info me-1"></i> {{ $question->explanation }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted"><i class="bi bi-question-circle fs-1 d-block mb-3"></i><p>No questions yet. Add your first question!</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('quizzes.questions.store', [$course, $quiz]) }}">@csrf
                <div class="modal-header"><h5 class="modal-title">Add Question</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <textarea name="question_text" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Question Type</label>
                            <select name="question_type" class="form-select" id="questionTypeSelect" onchange="toggleQuestionOptions()">
                                <option value="mcq">Multiple Choice (4 options)</option>
                                <option value="true_false">True / False</option>
                                <option value="fill_blank">Fill in the Blank</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="easy">Easy</option>
                                <option value="medium" selected>Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marks</label>
                            <input type="number" name="marks" class="form-control" value="10" min="1" step="0.5">
                        </div>
                    </div>

                    <div id="mcqOptions">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Option A</label>
                                <input type="text" name="option_a" id="optionA" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Option B</label>
                                <input type="text" name="option_b" id="optionB" class="form-control" required>
                            </div>
                            <div class="col-6" id="optionCGroup">
                                <label class="form-label">Option C</label>
                                <input type="text" name="option_c" id="optionC" class="form-control">
                            </div>
                            <div class="col-6" id="optionDGroup">
                                <label class="form-label">Option D</label>
                                <input type="text" name="option_d" id="optionD" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div id="trueFalseOptions" class="d-none">
                        <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>True/False questions have predefined options: <strong>True</strong> and <strong>False</strong>.</div>
                        <input type="hidden" name="option_a" value="True">
                        <input type="hidden" name="option_b" value="False">
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Correct Answer</label>
                        <select name="correct_answer" class="form-select" id="correctAnswerSelect">
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Explanation (optional)</label>
                        <textarea name="explanation" class="form-control" rows="2" placeholder="Explain why this answer is correct..."></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Add Question</button></div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleQuestionOptions() {
    const type = document.getElementById('questionTypeSelect').value;
    const mcqDiv = document.getElementById('mcqOptions');
    const tfDiv = document.getElementById('trueFalseOptions');
    const correctSelect = document.getElementById('correctAnswerSelect');
    const optionCGroup = document.getElementById('optionCGroup');
    const optionDGroup = document.getElementById('optionDGroup');

    if (type === 'true_false') {
        mcqDiv.classList.add('d-none');
        tfDiv.classList.remove('d-none');
        optionCGroup.classList.add('d-none');
        optionDGroup.classList.add('d-none');
        document.getElementById('optionA').removeAttribute('required');
        document.getElementById('optionB').removeAttribute('required');
        correctSelect.innerHTML = '<option value="a">True</option><option value="b">False</option>';
    } else if (type === 'mcq') {
        mcqDiv.classList.remove('d-none');
        tfDiv.classList.add('d-none');
        optionCGroup.classList.remove('d-none');
        optionDGroup.classList.remove('d-none');
        document.getElementById('optionA').setAttribute('required', 'required');
        document.getElementById('optionB').setAttribute('required', 'required');
        correctSelect.innerHTML = '<option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>';
    } else {
        mcqDiv.classList.add('d-none');
        tfDiv.classList.add('d-none');
        optionCGroup.classList.add('d-none');
        optionDGroup.classList.add('d-none');
        document.getElementById('optionA').removeAttribute('required');
        document.getElementById('optionB').removeAttribute('required');
        correctSelect.innerHTML = '<option value="a">A</option><option value="b">B</option>';
    }
}
</script>
@endpush
@endsection
