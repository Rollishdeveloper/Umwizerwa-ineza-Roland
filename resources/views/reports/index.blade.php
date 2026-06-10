@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Reports</h4><p class="text-muted mb-0">Generate, view, and export reports</p></div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body">
                    <div class="display-4 text-primary mb-3"><i class="bi bi-people-fill"></i></div>
                    <h5 class="fw-bold">Student Reports</h5>
                    <p class="text-muted small">Enrollment, progress, and performance reports</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.students') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i> View Report</a>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('reports.export-pdf', 'students') }}" class="btn btn-outline-danger"><i class="bi bi-filetype-pdf"></i> PDF</a>
                            <a href="{{ route('reports.export-csv', 'students') }}" class="btn btn-outline-success"><i class="bi bi-filetype-csv"></i> CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body">
                    <div class="display-4 text-success mb-3"><i class="bi bi-book-fill"></i></div>
                    <h5 class="fw-bold">Course Reports</h5>
                    <p class="text-muted small">Active courses, completion rates, statistics</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.courses') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i> View Report</a>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('reports.export-pdf', 'courses') }}" class="btn btn-outline-danger"><i class="bi bi-filetype-pdf"></i> PDF</a>
                            <a href="{{ route('reports.export-csv', 'courses') }}" class="btn btn-outline-success"><i class="bi bi-filetype-csv"></i> CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body">
                    <div class="display-4 text-warning mb-3"><i class="bi bi-person-badge"></i></div>
                    <h5 class="fw-bold">Instructor Reports</h5>
                    <p class="text-muted small">Instructor performance and student success</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.instructors') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i> View Report</a>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('reports.export-csv', 'instructors') }}" class="btn btn-outline-success"><i class="bi bi-filetype-csv"></i> CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card h-100 text-center">
                <div class="card-body">
                    <div class="display-4 text-info mb-3"><i class="bi bi-graph-up"></i></div>
                    <h5 class="fw-bold">Certificates</h5>
                    <p class="text-muted small">Certificate records for all students</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.export-pdf', 'certificates') }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-filetype-pdf"></i> Export PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card stat-card mt-4">
        <div class="card-header bg-transparent border-0">
            <h6 class="fw-bold mb-0"><i class="bi bi-download me-2"></i>Quick Export</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('reports.export-pdf', 'students') }}" class="btn btn-outline-danger w-100 mb-2">
                        <i class="bi bi-filetype-pdf"></i> Students PDF
                    </a>
                    <a href="{{ route('reports.export-csv', 'students') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv"></i> Students CSV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.export-pdf', 'courses') }}" class="btn btn-outline-danger w-100 mb-2">
                        <i class="bi bi-filetype-pdf"></i> Courses PDF
                    </a>
                    <a href="{{ route('reports.export-csv', 'courses') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv"></i> Courses CSV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.export-csv', 'instructors') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-filetype-csv"></i> Instructors CSV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.export-pdf', 'certificates') }}" class="btn btn-outline-danger w-100">
                        <i class="bi bi-filetype-pdf"></i> Certificates PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
