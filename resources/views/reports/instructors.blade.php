@extends('layouts.app')

@section('title', 'Instructor Reports')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Instructor Reports</h4><p class="text-muted mb-0">Instructor performance analytics</p></div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Instructor</th><th>Specialization</th><th>Courses</th><th>Total Students</th><th>Avg Completion</th></tr></thead>
                    <tbody>
                        @foreach($instructors as $instructor)
                            @php
                                $totalStudents = 0;
                                $totalCompletion = 0;
                                $courseCount = $instructor->courses->count();
                                foreach($instructor->courses as $course) {
                                    $totalStudents += $course->enrollments->count();
                                    $totalCompletion += $course->enrollments->where('status', 'completed')->count();
                                }
                                $avgCompletion = $totalStudents > 0 ? round(($totalCompletion / $totalStudents) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ $instructor->name }}</td>
                                <td>{{ $instructor->specialization ?? 'N/A' }}</td>
                                <td>{{ $courseCount }}</td>
                                <td>{{ $totalStudents }}</td>
                                <td>{{ $avgCompletion }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
