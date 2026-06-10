@extends('layouts.app')

@section('title', 'Instructor Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Instructor Management</h4><p class="text-muted mb-0">Manage instructors</p></div>
        <a href="{{ route('admin.instructors.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Add Instructor</a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Specialization</th><th>Courses</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($instructors as $instructor)
                            <tr>
                                <td>{{ $instructor->instructor_id }}</td>
                                <td class="fw-medium">{{ $instructor->name }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td>{{ $instructor->specialization ?? 'N/A' }}</td>
                                <td>{{ $instructor->courses->count() }}</td>
                                <td><span class="badge bg-{{ $instructor->user->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($instructor->user->status) }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.instructors.show', $instructor) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.instructors.edit', $instructor) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('admin.instructors.destroy', $instructor) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No instructors found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $instructors->links() }}
        </div>
    </div>
</div>
@endsection
