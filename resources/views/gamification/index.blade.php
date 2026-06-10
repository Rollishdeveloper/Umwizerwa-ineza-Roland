@extends('layouts.app')

@section('title', 'Gamification - Rewards')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-trophy-fill text-warning me-2"></i>Gamification</h4>
            <p class="text-muted mb-0">Track your points, badges, achievements and climb the leaderboard</p>
        </div>
    </div>

    <!-- Stats & Level Card -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-3"><i class="bi bi-star-fill"></i></div>
                    <div>
                        <h6 class="text-white-50">Level {{ $summary['level'] }}</h6>
                        <h2 class="fw-bold mb-0">{{ number_format($summary['points']) }}</h2>
                        <small>Total Points</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-3"><i class="bi bi-award-fill"></i></div>
                    <div>
                        <h6>Badges Earned</h6>
                        <h2 class="fw-bold mb-0">{{ $summary['badges'] }}</h2>
                        <small>of {{ $badges->count() }} total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="display-3"><i class="bi bi-trophy-fill"></i></div>
                    <div>
                        <h6 class="text-white-50">Achievements</h6>
                        <h2 class="fw-bold mb-0">{{ $summary['achievements'] }}</h2>
                        <small>of {{ $achievements->count() }} total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Level Progress -->
    <div class="card stat-card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                    Level {{ $summary['level'] }} → Level {{ $summary['level'] + 1 }}
                </h6>
                <span class="text-muted small">{{ number_format($summary['points_to_next_level']) }} points needed</span>
            </div>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" 
                     role="progressbar" style="width: {{ $summary['progress_percentage'] }}%"
                     aria-valuenow="{{ $summary['progress_percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted">{{ number_format($summary['points']) }} / {{ number_format($summary['next_level_points']) }} XP</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- Badges Section -->
        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-award-fill text-warning me-2"></i>Badges</h6>
                    <a href="{{ route('gamification.badges') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($badges->take(6) as $badge)
                            @php $earned = $studentBadges->contains('badge_id', $badge->badge_id); @endphp
                            <div class="col-4 col-md-3 text-center">
                                <div class="badge-item {{ $earned ? 'earned' : 'locked' }}" 
                                     style="cursor: pointer;" title="{{ $badge->name }}: {{ $badge->description }}">
                                    <div class="badge-icon rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2"
                                         style="width: 60px; height: 60px; background: {{ $earned ? $badge->color : '#e9ecef' }}; 
                                                {{ $earned ? 'box-shadow: 0 0 20px rgba(' . hexToRgb($badge->color) . ',0.3);' : '' }}">
                                        <i class="bi {{ $badge->icon ?? 'bi-award-fill' }} text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <small class="d-block text-{{ $earned ? 'dark' : 'muted' }}" style="font-size: 0.7rem;">
                                        {{ $earned ? $badge->name : '???' }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">No badges available yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievements Section -->
        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-success me-2"></i>Achievements</h6>
                    <a href="{{ route('gamification.achievements') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($achievements->take(6) as $achievement)
                            @php $earned = $studentAchievements->contains('achievement_id', $achievement->achievement_id); @endphp
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded {{ $earned ? 'bg-success bg-opacity-10' : 'bg-light' }}">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 40px; height: 40px; background: {{ $earned ? $achievement->color : '#e9ecef' }};">
                                        <i class="bi {{ $achievement->icon ?? 'bi-trophy-fill' }} text-white"></i>
                                    </div>
                                    <div class="min-width-0">
                                        <p class="mb-0 fw-medium small">{{ $earned ? $achievement->name : '???' }}</p>
                                        <small class="text-{{ $earned ? 'success' : 'muted' }}" style="font-size: 0.7rem;">
                                            {{ $earned ? 'Unlocked' : 'Locked' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">No achievements yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard Preview -->
    <div class="card stat-card mt-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Leaderboard - Top Students</h6>
            <a href="{{ route('gamification.leaderboard') }}" class="btn btn-sm btn-outline-primary">Full Board</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Level</th>
                            <th>Points</th>
                            <th>Badges</th>
                            <th>Achievements</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $index => $entry)
                            @php $entry = (object) $entry; @endphp
                            <tr class="{{ $student && $entry->student_id === $student->student_id ? 'table-active fw-bold' : '' }}">
                                <td>
                                    @if($index < 3)
                                        <i class="bi bi-{{ ['trophy-fill text-warning', 'trophy-fill text-secondary', 'trophy-fill text-danger'][$index] }}"></i>
                                    @endif
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 30px; height: 30px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($entry->name, 0, 1)) }}
                                        </div>
                                        {{ $entry->name }}
                                    </div>
                                </td>
                                <td><span class="badge bg-warning text-dark">Lv.{{ $entry->level }}</span></td>
                                <td class="fw-bold">{{ number_format($entry->points) }}</td>
                                <td>{{ $entry->badges_count ?? $entry->badges()->count() }}</td>
                                <td>{{ $entry->achievements_count ?? $entry->achievements()->count() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No students yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "$r,$g,$b";
}
@endphp
