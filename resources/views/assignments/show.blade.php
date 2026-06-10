@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
<div class="fade-in">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $assignment->title }}</h6>
                    <p>{{ $assignment->description ?? 'No description' }}</p>
                    <div class="d-flex justify-content-between mb-2"><span>Total Marks:</span><span class="fw-bold">{{ $assignment->total_marks }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Due Date:</span><span class="fw-bold">{{ $assignment->due_date ? $assignment->due_date->format('M d, Y H:i') : 'N/A' }}</span></div>
                    <div class="d-flex justify-content-between mb-3"><span>Submissions:</span><span class="fw-bold">{{ $assignment->submissions->count() }}</span></div>

                    @if(auth()->user()->isStudent())
                        @php $submission = $assignment->submissions->where('student_id', auth()->user()->student->student_id ?? 0)->first(); @endphp
                        @if($submission)
                            <div class="alert alert-info">
                                <i class="bi bi-check-circle"></i> Submitted {{ $submission->submitted_at->diffForHumans() }}
                                @if($submission->marks !== null)
                                    <div class="mt-2 fw-bold">Grade: {{ $submission->marks }}/{{ $assignment->total_marks }}</div>
                                @endif
                            </div>
                        @else
                            <form method="POST" action="{{ route('assignments.submit', [$course, $assignment]) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3"><label class="form-label">Upload File</label><input type="file" name="file" class="form-control" required></div>
                                <div class="mb-3"><textarea name="notes" class="form-control" placeholder="Notes (optional)" rows="2"></textarea></div>
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload"></i> Submit Assignment</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            @if(auth()->user()->isInstructor())
                <div class="card stat-card">
                    <div class="card-header bg-transparent border-0"><h6 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Submissions ({{ $assignment->submissions->count() }})</h6></div>
                    <div class="card-body p-0">
                        @forelse($assignment->submissions as $sub)
                            <div class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="fw-medium mb-1">{{ $sub->student->name ?? 'Unknown' }}</p>
                                        <small class="text-muted">Submitted: {{ $sub->submitted_at->diffForHumans() }}</small>
                                        @if($sub->notes)<p class="small text-muted mt-1">{{ $sub->notes }}</p>@endif
                                    </div>
                                    <div>
                                        @if($sub->marks !== null)
                                            <span class="badge bg-success fs-6">{{ $sub->marks }}/{{ $assignment->total_marks }}</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </div>
                                </div>
                                @if($sub->marks === null)
                                    <form method="POST" action="{{ route('assignments.grade', [$course, $assignment, $sub]) }}" class="mt-2 row g-2">
                                        @csrf
                                        <div class="col-md-6"><input type="number" name="marks" class="form-control form-control-sm" placeholder="Marks (max {{ $assignment->total_marks }})" min="0" max="{{ $assignment->total_marks }}" step="0.01" required></div>
                                        <div class="col-md-6"><input type="text" name="feedback" class="form-control form-control-sm" placeholder="Feedback"></div>
                                        <div class="col-12"><button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check"></i> Grade</button></div>
                                    </form>
                                @elseif($sub->feedback)
                                    <div class="mt-2 p-2 bg-light rounded"><small><i class="bi bi-chat"></i> {{ $sub->feedback }}</small></div>
                                @endif
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">No submissions yet</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
