@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Leaderboard</h4>
            <p class="text-muted mb-0">Top performing students ranked by experience points</p>
        </div>
        <a href="{{ route('gamification.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Podium for Top 3 -->
    <div class="row g-3 mb-4 justify-content-center">
        @foreach($leaderboard->take(3) as $index => $entry)
            @php $entry = (object) $entry; @endphp
            <div class="col-md-3">
                <div class="card stat-card text-center h-100 {{ $index === 0 ? 'border-warning' : ($index === 1 ? 'border-secondary' : 'border-danger') }}">
                    <div class="card-body">
                        <div class="display-4 mb-2">
                            <i class="bi bi-{{ ['trophy-fill text-warning', 'trophy-fill text-secondary', 'trophy-fill text-danger'][$index] }}"></i>
                        </div>
                        <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-2"
                             style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ strtoupper(substr($entry->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold">{{ $entry->name }}</h6>
                        <span class="badge bg-warning text-dark mb-2">Level {{ $entry->level }}</span>
                        <h4 class="fw-bold text-primary">{{ number_format($entry->points) }}</h4>
                        <small class="text-muted">points</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Full Leaderboard Table -->
    <div class="card stat-card">
        <div class="card-header bg-transparent border-0">
            <h6 class="fw-bold mb-0"><i class="bi bi-list-ol me-2"></i>Full Rankings</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Level</th>
                            <th>Points</th>
                            <th>Courses</th>
                            <th>Quizzes Passed</th>
                            <th>Certificates</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $index => $entry)
                            @php $entry = (object) $entry; @endphp
                            <tr class="{{ isset($student) && $entry->student_id === $student->student_id ? 'table-active fw-bold' : '' }}">
                                <td>
                                    @if($index === 0)
                                        <i class="bi bi-trophy-fill text-warning"></i>
                                    @elseif($index === 1)
                                        <i class="bi bi-trophy-fill text-secondary"></i>
                                    @elseif($index === 2)
                                        <i class="bi bi-trophy-fill text-danger"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 32px; height: 32px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($entry->name, 0, 1)) }}
                                        </div>
                                        {{ $entry->name }}
                                        @if(isset($student) && $entry->student_id === $student->student_id)
                                            <span class="badge bg-info">You</span>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="badge bg-warning text-dark">Lv.{{ $entry->level }}</span></td>
                                <td class="fw-bold">{{ number_format($entry->points) }}</td>
                                <td>{{ $entry->enrollments->where('status', 'completed')->count() }}</td>
                                <td>{{ $entry->quizResults->where('status', 'passed')->count() }}</td>
                                <td>{{ $entry->certificates->count() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No students found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(isset($studentRank))
        <div class="card stat-card mt-3">
            <div class="card-body text-center">
                <h6 class="fw-bold mb-0">Your Rank: #{{ $studentRank }} of {{ $leaderboard->count() }}</h6>
                <small class="text-muted">{{ number_format($student->points) }} total points</small>
            </div>
        </div>
    @endif
</div>
@endsection
