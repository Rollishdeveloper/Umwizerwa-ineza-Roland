@extends('layouts.app')

@section('title', 'Edit Instructor')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Edit Instructor</h4><p class="text-muted mb-0">{{ $instructor->name }}</p></div>
        <a href="{{ route('admin.instructors.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}">@csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3"><label class="form-label fw-medium">Name</label><input type="text" name="name" class="form-control" value="{{ $instructor->name }}"></div>
                        <div class="mb-3"><label class="form-label fw-medium">Email</label><input type="email" name="email" class="form-control" value="{{ $instructor->email }}"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3"><label class="form-label fw-medium">Specialization</label><input type="text" name="specialization" class="form-control" value="{{ $instructor->specialization }}"></div>
                        <div class="mb-3"><label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['active','inactive','suspended'] as $s)
                                    <option value="{{ $s }}" {{ $instructor->user->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label fw-medium">Biography</label><textarea name="biography" class="form-control" rows="3">{{ $instructor->biography }}</textarea></div>
                <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
