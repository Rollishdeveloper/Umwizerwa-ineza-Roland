@extends('layouts.app')

@section('title', 'All Achievements')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-trophy-fill text-success me-2"></i>All Achievements</h4>
            <p class="text-muted mb-0">Reach milestones to unlock achievements and earn bonus points</p>
        </div>
        <a href="{{ route('gamification.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        @forelse($achievements as $achievement)
            @php $earned = $studentAchievements->contains('achievement_id', $achievement->achievement_id); @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card stat-card h-100 {{ $earned ? 'border-success' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 50px; height: 50px; background: {{ $earned ? $achievement->color : '#e9ecef' }};">
                                <i class="bi {{ $achievement->icon ?? 'bi-trophy-fill' }} text-white" style="font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">{{ $earned ? $achievement->name : '???' }}</h6>
                                <p class="small text-muted mb-2">
                                    @if($earned)
                                        {{ $achievement->description }}
                                    @else
                                        Reach the required milestone to unlock this achievement.
                                    @endif
                                </p>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-{{ $earned ? 'success' : 'secondary' }}">
                                        <i class="bi bi-{{ $earned ? 'check' : 'lock' }}-fill me-1"></i>
                                        {{ $earned ? 'Unlocked' : 'Locked' }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        {{ $achievement->students_count }} unlocked
                                    </span>
                                    @if($earned)
                                        <span class="badge bg-info text-white">
                                            <i class="bi bi-star-fill"></i> +30 pts
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-trophy fs-1 d-block mb-3"></i>
                <h5>No achievements available</h5>
            </div>
        @endforelse
    </div>
</div>
@endsection
