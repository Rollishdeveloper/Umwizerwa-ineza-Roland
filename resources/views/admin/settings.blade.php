@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">System Settings</h4><p class="text-muted mb-0">Configure system preferences</p></div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>System Information</h6>
                    <div class="mb-2"><small class="text-muted">Application</small><p class="fw-medium">E-Learning Management System</p></div>
                    <div class="mb-2"><small class="text-muted">Laravel Version</small><p class="fw-medium">{{ Illuminate\Foundation\Application::VERSION }}</p></div>
                    <div class="mb-2"><small class="text-muted">PHP Version</small><p class="fw-medium">{{ PHP_VERSION }}</p></div>
                    <div class="mb-2"><small class="text-muted">Database</small><p class="fw-medium">SQLite (Development)</p></div>
                    <div class="mb-2"><small class="text-muted">Environment</small><p class="fw-medium">{{ app()->environment() }}</p></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-tools me-2"></i>Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start" onclick="event.preventDefault(); if(confirm('Clear all application cache?')) { document.getElementById('clear-cache-form').submit(); }"><i class="bi bi-arrow-repeat me-2"></i> Clear Application Cache</button>
                        <form id="clear-cache-form" action="{{ route('admin.settings') }}" method="POST" class="d-none">@csrf
                            <input type="hidden" name="clear_cache" value="1">
                        </form>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary text-start"><i class="bi bi-file-text me-2"></i> Generate Reports</a>
                        <button class="btn btn-outline-primary text-start" onclick="window.print()"><i class="bi bi-printer me-2"></i> Print This Page</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
