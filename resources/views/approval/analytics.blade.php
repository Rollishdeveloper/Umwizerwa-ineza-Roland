@extends('layouts.app')
@section('title', 'Approval Analytics')
@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1"><i class="bi bi-bar-chart text-primary me-2"></i>Approval Analytics</h4><p class="text-muted mb-0">Track course generation and approval metrics</p></div>
        <a href="{{ route('approval.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6"><div class="card stat-card bg-primary text-white"><div class="card-body"><h6 class="text-white-50">Total Generated</h6><h2 class="fw-bold mb-0">{{ $analytics['total_generated'] }}</h2></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card bg-warning text-dark"><div class="card-body"><h6>Pending Review</h6><h2 class="fw-bold mb-0">{{ $analytics['pending_review'] }}</h2></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card bg-success text-white"><div class="card-body"><h6 class="text-white-50">Approved</h6><h2 class="fw-bold mb-0">{{ $analytics['approved'] }}</h2></div></div></div>
        <div class="col-md-3 col-6"><div class="card stat-card bg-danger text-white"><div class="card-body"><h6 class="text-white-50">Rejected</h6><h2 class="fw-bold mb-0">{{ $analytics['rejected'] }}</h2></div></div></div>
    </div>
    <div class="row g-4">
        <div class="col-md-6"><div class="card stat-card"><div class="card-body"><h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Workflows by Stage</h6>
            @foreach($workflowsByStage as $stage => $count)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ str_replace('_', ' ', ucfirst($stage)) }}</span>
                    <div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1" style="width:200px;height:10px;"><div class="progress-bar" style="width:{{ $analytics['total_generated'] > 0 ? ($count/$analytics['total_generated']*100) : 0 }}%"></div></div><span class="fw-bold">{{ $count }}</span></div>
                </div>
            @endforeach
        </div></div></div>
        <div class="col-md-6"><div class="card stat-card"><div class="card-body"><h6 class="fw-bold mb-3"><i class="bi bi-activity me-2"></i>Review Performance</h6>
            <div class="d-flex justify-content-between mb-2"><span>Avg Review Time</span><span class="fw-bold">{{ number_format($analytics['avg_review_time'], 1) }} hours</span></div>
            <div class="d-flex justify-content-between mb-2"><span>AI Accuracy Rate</span><span class="fw-bold">{{ number_format($analytics['ai_accuracy'], 1) }}%</span></div>
            <hr>
            <h6 class="fw-bold mb-2">Reviews by Type</h6>
            @foreach($reviewsByType as $r)
                <div class="d-flex justify-content-between small mb-1">
                    <span><span class="badge bg-{{ $r->status === 'approved' ? 'success' : ($r->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($r->status) }}</span> {{ ucfirst($r->review_type) }}</span>
                    <span class="fw-bold">{{ $r->total }}</span>
                </div>
            @endforeach
        </div></div></div>
    </div>
</div>
@endsection
