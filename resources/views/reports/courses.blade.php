@extends('layouts.app')

@section('title', 'Course Reports')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Course Reports</h4><p class="text-muted mb-0">Course analytics and statistics</p></div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Course</th><th>Instructor</th><th>Category</th><th>Level</th><th>Status</th><th>Students</th><th>Completion</th></tr></thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td class="fw-medium">{{ $course->title }}</td>
                                <td>{{ $course->instructor->name ?? 'N/A' }}</td>
                                <td>{{ $course->category->category_name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark">{{ ucfirst($course->level) }}</span></td>
                                <td><span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($course->status) }}</span></td>
                                <td>{{ $course->enrollments->count() }}</td>
                                <td>
                                    @php $completed = $course->enrollments->where('status', 'completed')->count(); @endphp
                                    {{ $course->enrollments->count() > 0 ? round(($completed / $course->enrollments->count()) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
