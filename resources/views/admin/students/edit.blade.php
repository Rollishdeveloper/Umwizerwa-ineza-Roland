@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Edit Student</h4><p class="text-muted mb-0">{{ $student->name }}</p></div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.students.update', $student) }}">@csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3"><label class="form-label fw-medium">Full Name</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $student->name) }}"></div>
                        <div class="mb-3"><label class="form-label fw-medium">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}"></div>
                        <div class="mb-3"><label class="form-label fw-medium">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3"><label class="form-label fw-medium">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                @foreach(['male','female','other'] as $g)
                                    <option value="{{ $g }}" {{ ($student->gender ?? '') === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label fw-medium">Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d')) }}"></div>
                        <div class="mb-3"><label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['active','inactive','suspended'] as $s)
                                    <option value="{{ $s }}" {{ $student->user->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Student</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
