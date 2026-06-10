@extends('layouts.app')

@section('title', 'All Badges')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-award-fill text-warning me-2"></i>All Badges</h4>
            <p class="text-muted mb-0">Complete actions to earn badges and show off your achievements</p>
        </div>
        <a href="{{ route('gamification.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        @forelse($badges as $badge)
            @php $earned = $studentBadges->contains('badge_id', $badge->badge_id); @endphp
            <div class="col-md-4 col-lg-3">
                <div class="card stat-card text-center h-100 {{ $earned ? 'border-warning' : 'opacity-75' }}">
                    <div class="card-body">
                        <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px; background: {{ $earned ? $badge->color : '#e9ecef' }};">
                            <i class="bi {{ $badge->icon ?? 'bi-award-fill' }} text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h6 class="fw-bold {{ $earned ? '' : 'text-muted' }}">{{ $earned ? $badge->name : '???' }}</h6>
                        <p class="small text-muted mb-2">
                            @if($earned)
                                {{ $badge->description }}
                            @else
                                Complete the required actions to unlock this badge.
                            @endif
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge bg-{{ $earned ? 'success' : 'secondary' }}">
                                {{ $earned ? 'Earned' : 'Locked' }}
                            </span>
                            <span class="badge bg-light text-dark">{{ $badge->students_count }} earned</span>
                        </div>
                        @if($earned)
                            <div class="mt-2 small text-success">
                                <i class="bi bi-check-circle-fill"></i> Awarded 
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-award fs-1 d-block mb-3"></i>
                <h5>No badges available</h5>
            </div>
        @endforelse
    </div>
</div>
@endsection
