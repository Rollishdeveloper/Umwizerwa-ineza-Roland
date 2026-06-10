@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Student Management</h4>
            <p class="text-muted mb-0">Manage all registered students</p>
        </div>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Add Student
        </a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Student #</th>
                            <th>Enrollments</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                </td>
                                <td class="fw-medium">{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td><code>{{ $student->student_number }}</code></td>
                                <td>{{ $student->enrollments->count() }}</td>
                                <td>
                                    <span class="badge bg-{{ $student->user->status === 'active' ? 'success' : ($student->user->status === 'inactive' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($student->user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" 
                                              onsubmit="return confirm('Delete this student?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No students found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
