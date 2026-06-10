@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Certificates</h4><p class="text-muted mb-0">Your earned certificates</p></div>
        <div>
            <form method="GET" action="{{ route('certificates.verify') }}" class="d-flex gap-2">
                <input type="text" name="certificate_number" class="form-control form-control-sm" placeholder="Verify certificate...">
                <button type="submit" class="btn btn-outline-primary btn-sm">Verify</button>
            </form>
        </div>
    </div>
    <div class="row g-4">
        @forelse($certificates as $cert)
            <div class="col-md-6 col-lg-4">
                <div class="card stat-card text-center h-100">
                    <div class="card-body">
                        <div class="display-3 text-warning mb-3"><i class="bi bi-award-fill"></i></div>
                        <h6 class="fw-bold mb-1">{{ $cert->course->title ?? 'Course' }}</h6>
                        <p class="text-muted small mb-2">Certificate #: <code>{{ $cert->certificate_number }}</code></p>
                        <p class="text-muted small mb-3"><i class="bi bi-calendar"></i> Issued: {{ $cert->issue_date->format('F d, Y') }}</p>
                        <a href="{{ route('certificates.show', $cert) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye"></i> View Certificate
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-award fs-1 d-block mb-3"></i>
                <h5>No certificates yet</h5>
                <p>Complete a course to earn your certificate!</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary">Browse Courses</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
