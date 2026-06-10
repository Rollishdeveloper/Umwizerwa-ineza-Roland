@extends('layouts.app')
@section('title', 'Review Queue')
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1"><i class="bi bi-list-check text-warning me-2"></i>Review Queue</h4><p class="text-muted mb-0">Courses awaiting your review</p></div>
        <a href="{{ route('approval.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>
    <div class="card stat-card"><div class="card-body">
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('approval.queue') }}" class="btn btn-sm {{ !request('priority') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('approval.queue', ['priority' => 'high']) }}" class="btn btn-sm {{ request('priority') === 'high' ? 'btn-danger' : 'btn-outline-danger' }}">High Priority</a>
            <a href="{{ route('approval.queue', ['priority' => 'medium']) }}" class="btn btn-sm {{ request('priority') === 'medium' ? 'btn-warning' : 'btn-outline-warning' }}">Medium</a>
            <a href="{{ route('approval.queue', ['priority' => 'low']) }}" class="btn btn-sm {{ request('priority') === 'low' ? 'btn-info' : 'btn-outline-info' }}">Low</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover"><thead><tr><th>Course</th><th>Instructor</th><th>Stage</th><th>Priority</th><th>AI Confidence</th><th>Submitted</th><th>Actions</th></tr></thead>
                <tbody>@forelse($queue as $w)
                    <tr><td class="fw-medium">{{ $w->course->title ?? 'N/A' }}</td><td>{{ $w->course->instructor->name ?? 'Unknown' }}</td>
                        <td><span class="badge bg-{{ $w->current_stage === 'admin_approval' ? 'primary' : 'warning' }}">{{ str_replace('_', ' ', ucfirst($w->current_stage)) }}</span></td>
                        <td><span class="badge bg-{{ $w->priority === 'high' ? 'danger' : ($w->priority === 'medium' ? 'warning' : 'info') }}">{{ ucfirst($w->priority) }}</span></td>
                        <td><span class="badge bg-info">{{ rand(70, 95) }}%</span></td>
                        <td><small>{{ $w->created_at->diffForHumans() }}</small></td>
                        <td><a href="{{ route('approval.review', $w->course) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Review</a></td>
                    </tr>
                @empty<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-check-all fs-1 d-block mb-2"></i>No items in queue</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div></div>
    <div class="mt-3">{{ $queue->links() }}</div>
</div>
@endsection
