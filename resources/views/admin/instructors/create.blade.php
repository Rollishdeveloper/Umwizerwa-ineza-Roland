@extends('layouts.app')

@section('title', 'Add Instructor')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Add New Instructor</h4><p class="text-muted mb-0">Create a new instructor account</p></div>
        <a href="{{ route('admin.instructors.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.instructors.store') }}">@csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3"><label class="form-label fw-medium">Full Name</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-medium">Email</label><input type="email" name="email" class="form-control" required></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3"><label class="form-label fw-medium">Specialization</label><input type="text" name="specialization" class="form-control"></div>
                        <div class="mb-3"><label class="form-label fw-medium">Biography</label><textarea name="biography" class="form-control" rows="3"></textarea></div>
                    </div>
                </div>
                <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Create Instructor</button></div>
            </form>
            <div class="alert alert-info mt-3"><i class="bi bi-info-circle"></i> Default password is <strong>password123</strong>.</div>
        </div>
    </div>
</div>
@endsection
