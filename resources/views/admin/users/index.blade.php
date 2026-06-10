@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">User Management</h4><p class="text-muted mb-0">All system users</p></div>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Username</th><th>Role</th><th>Status</th><th>Last Login</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->username ?? 'N/A' }}</td>
                                <td><span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'instructor' ? 'primary' : 'success') }}">{{ ucfirst($user->role) }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="d-flex gap-1">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" style="width: auto;">
                                            @foreach(['active','inactive','suspended'] as $status)
                                                <option value="{{ $status }}" {{ $user->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-check"></i></button>
                                    </form>
                                </td>
                                <td>{{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }}</td>
                                <td>
                                    @if($user->isStudent())
                                        <a href="{{ route('admin.students.show', $user->student) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    @elseif($user->isInstructor())
                                        <a href="{{ route('admin.instructors.show', $user->instructor) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No users found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
