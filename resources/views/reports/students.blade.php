@extends('layouts.app')

@section('title', 'Student Reports')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Student Reports</h4><p class="text-muted mb-0">Student analytics and performance</p></div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Student</th><th>Email</th><th>Enrollments</th><th>Completed</th><th>Avg Quiz Score</th><th>Certificates</th></tr></thead>
                    <tbody>
                        @foreach($students as $student)
                            @php
                                $enrollmentsCount = $student->enrollments->count();
                                $completedCount = $student->enrollments->where('status', 'completed')->count();
                                $avgScore = $student->quizResults->avg('percentage');
                            @endphp
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td class="fw-medium">{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $enrollmentsCount }}</td>
                                <td>{{ $completedCount }}</td>
                                <td>{{ $avgScore ? number_format($avgScore, 1) . '%' : 'N/A' }}</td>
                                <td>{{ $student->certificates->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
