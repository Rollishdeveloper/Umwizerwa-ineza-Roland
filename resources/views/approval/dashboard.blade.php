@extends('layouts.app')
@section('title', 'Approval Dashboard')
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1"><i class="bi bi-check2-circle text-success me-2"></i>Course Approval Dashboard</h4><p class="text-muted mb-0">Review and approve AI-generated courses</p></div>
        <a href="{{ route('approval.analytics') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-bar-chart"></i> Analytics</a>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6"><div class="card stat-card bg-warning text-dark"><div class="card-body"><h6>Pending Review</h6><h2 class="fw-bold mb-0">{{ $stats['pending'] }}</h2></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card bg-info text-white"><div class="card-body"><h6 class="text-white-50">Needs Review</h6><h2 class="fw-bold mb-0">{{ $stats['needs_review'] }}</h2></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card bg-success text-white"><div class="card-body"><h6 class="text-white-50">Published</h6><h2 class="fw-bold mb-0">{{ $stats['published'] }}</h2></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card bg-danger text-white"><div class="card-body"><h6 class="text-white-50">Rejected</h6><h2 class="fw-bold mb-0">{{ $stats['rejected'] }}</h2></div></div></div>
    </div>
    <div class="card stat-card"><div class="card-body">
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('approval.dashboard') }}" class="btn btn-sm {{ !request('stage') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('approval.dashboard', ['stage' => 'pending_review']) }}" class="btn btn-sm {{ request('stage') === 'pending_review' ? 'btn-primary' : 'btn-outline-primary' }}">Pending Review</a>
            <a href="{{ route('approval.dashboard', ['stage' => 'admin_approval']) }}" class="btn btn-sm {{ request('stage') === 'admin_approval' ? 'btn-primary' : 'btn-outline-primary' }}">Admin Approval</a>
            <a href="{{ route('approval.dashboard', ['stage' => 'published']) }}" class="btn btn-sm {{ request('stage') === 'published' ? 'btn-primary' : 'btn-outline-primary' }}">Published</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Course</th><th>Instructor</th><th>Stage</th><th>Priority</th><th>Created</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($workflows as $w)
                        <tr>
                            <td class="fw-medium"><a href="{{ route('courses.show', $w->course) }}">{{ $w->course->title ?? 'N/A' }}</a></td>
                            <td>{{ $w->course->instructor->name ?? 'Unknown' }}</td>
                            <td><span class="badge bg-{{ $w->current_stage === 'published' ? 'success' : ($w->current_stage === 'rejected' ? 'danger' : ($w->current_stage === 'admin_approval' ? 'primary' : 'warning')) }}">{{ str_replace('_', ' ', ucfirst($w->current_stage)) }}</span></td>
                            <td><span class="badge bg-{{ $w->priority === 'high' ? 'danger' : ($w->priority === 'medium' ? 'warning' : 'info') }}">{{ ucfirst($w->priority) }}</span></td>
                            <td><small>{{ $w->created_at->diffForHumans() }}</small></td>
                            <td>
                                <a href="{{ route('courses.show', $w->course) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('approval.review', $w->course) }}" class="btn btn-sm btn-{{ $w->current_stage === 'published' ? 'outline-success' : 'outline-warning' }}">
                                    @if($w->current_stage === 'published')<i class="bi bi-check"></i>@else<i class="bi bi-pencil"></i> Review@endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No workflows found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
    <div class="mt-3">{{ $workflows->links() }}</div>
</div>
@endsection
