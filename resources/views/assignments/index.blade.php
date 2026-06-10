@extends('layouts.app')

@section('title', 'Assignments')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Assignments</h4><p class="text-muted mb-0">{{ $course->title }}</p></div>
        @if(auth()->user()->isInstructor())
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAssignmentModal"><i class="bi bi-plus-circle"></i> Add Assignment</button>
        @endif
    </div>
    <div class="row g-4">
        @forelse($assignments as $assignment)
            <div class="col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold">{{ $assignment->title }}</h6>
                            <span class="badge bg-primary">{{ $assignment->total_marks }} marks</span>
                        </div>
                        <p class="text-muted small mb-2">{{ Str::limit($assignment->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small><i class="bi bi-calendar"></i> Due: {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No deadline' }}</small>
                            <a href="{{ route('assignments.show', [$course, $assignment]) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted"><i class="bi bi-file-text fs-1 d-block mb-3"></i><p>No assignments yet</p></div>
        @endforelse
    </div>

    @if(auth()->user()->isInstructor())
        <!-- Add Assignment Modal -->
        <div class="modal fade" id="addAssignmentModal">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('assignments.store', $course) }}">@csrf
                    <div class="modal-header"><h5 class="modal-title">Add Assignment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                        <div class="mb-3"><label class="form-label">Due Date</label><input type="datetime-local" name="due_date" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Total Marks</label><input type="number" name="total_marks" class="form-control" value="100" min="1" step="0.01"></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Create</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
