@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Admin Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-text"></i> View Reports
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Students</h6>
                            <h2 class="fw-bold mb-0">{{ $totalStudents }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <small class="text-white-50"><i class="bi bi-arrow-up"></i> Registered learners</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Instructors</h6>
                            <h2 class="fw-bold mb-0">{{ $totalInstructors }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <small class="text-white-50"><i class="bi bi-arrow-up"></i> Active educators</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card stat-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Courses</h6>
                            <h2 class="fw-bold mb-0">{{ $totalCourses }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-book-fill"></i>
                        </div>
                    </div>
                    <small class="text-white-50"><i class="bi bi-arrow-up"></i> Available courses</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Enrollments</h6>
                            <h2 class="fw-bold mb-0">{{ $totalEnrollments }}</h2>
                        </div>
                        <div class="icon-circle bg-white bg-opacity-25">
                            <i class="bi bi-journal-check"></i>
                        </div>
                    </div>
                    <small class="text-white-50"><i class="bi bi-arrow-up"></i> Total enrollments</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-line me-2"></i>Enrollments by Month</h6>
                    <canvas id="enrollmentChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Course Completion Stats</h6>
                    <canvas id="completionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-activity me-2"></i>Recent Activities</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $log)
                            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                <div class="bg-light rounded-circle p-2">
                                    <i class="bi bi-person-circle text-muted"></i>
                                </div>
                                <div>
                                    <p class="mb-0 small">{{ $log->activity }}</p>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No recent activities
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-award me-2"></i>Certificates Issued</h6>
                </div>
                <div class="card-body text-center py-5">
                    <div class="display-1 fw-bold text-primary">{{ $totalCertificates }}</div>
                    <p class="text-muted">Total certificates issued to students</p>
                    <a href="{{ route('certificates.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye"></i> View Certificates
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enrollment Chart
    const enrollCtx = document.getElementById('enrollmentChart')?.getContext('2d');
    if (enrollCtx) {
        new Chart(enrollCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($enrollmentsByMonth->pluck('month')->map(function($m) { 
                    return date('F', mktime(0, 0, 0, (int)$m, 1)); 
                })) !!},
                datasets: [{
                    label: 'Enrollments',
                    data: {!! json_encode($enrollmentsByMonth->pluck('total')) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102,126,234,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Completion Chart
    const compCtx = document.getElementById('completionChart')?.getContext('2d');
    if (compCtx) {
        const compData = {!! json_encode($completionStats) !!};
        const labels = compData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1));
        const data = compData.map(d => d.total);
        const colors = ['#28a745', '#ffc107', '#17a2b8'];

        new Chart(compCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%',
            }
        });
    }
});
</script>
@endpush
