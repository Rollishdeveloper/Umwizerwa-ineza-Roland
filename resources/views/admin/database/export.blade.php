@extends('layouts.app')

@section('title', 'Database Export')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-database-gear me-2 text-primary"></i> Database Export</h4>
            <p class="text-muted mb-0">Export your SQLite database for backup or MySQL migration</p>
        </div>
        <div>
            <a href="{{ route('admin.database.import') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-upload"></i> Import to MySQL
            </a>
        </div>
    </div>

    {{-- Database Info Card --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card-premium" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Database Size</h6>
                            <h2 class="fw-bold mb-0">{{ $dbSize }} <small>KB</small></h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="bi bi-database fs-4"></i>
                        </div>
                    </div>
                    <small class="text-white-50">SQLite (Development)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card-premium" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Tables</h6>
                            <h2 class="fw-bold mb-0">{{ $tableCount }}</h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="bi bi-table fs-4"></i>
                        </div>
                    </div>
                    <small class="text-white-50">Data tables</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card-premium" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Driver</h6>
                            <h2 class="fw-bold mb-0">SQLite</h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3">
                            <i class="bi bi-gear-wide-connected fs-4"></i>
                        </div>
                    </div>
                    <small class="text-white-50">{{ app()->environment() }} environment</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Export Options --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #667eea20, #764ba220);">
                            <i class="bi bi-filetype-sql fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Option 1: SQL Export</h5>
                            <p class="text-muted small mb-0">Complete SQL dump with table structures and data inserts</p>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">
                        Generates <code>sqlite_export.sql</code> containing all table definitions,
                        data INSERT statements, and indexes. Compatible with MySQL import tools.
                    </p>
                    <a href="{{ route('admin.database.export.sql') }}" class="btn btn-primary w-100"
                       onclick="return confirm('Download SQL export of entire database?')">
                        <i class="bi bi-download me-2"></i> Download SQL Export
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #4facfe20, #00f2fe20);">
                            <i class="bi bi-filetype-csv fs-4 text-info"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Option 2: CSV Export</h5>
                            <p class="text-muted small mb-0">Each table exported as individual CSV file</p>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">
                        Downloads a ZIP archive containing one CSV per table (e.g.,
                        <code>users.csv</code>, <code>courses.csv</code>, <code>enrollments.csv</code>).
                    </p>
                    <a href="{{ route('admin.database.export.csv') }}" class="btn btn-info text-white w-100"
                       onclick="return confirm('Download CSV export of all tables?')">
                        <i class="bi bi-download me-2"></i> Download CSV Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #f093fb20, #f5576c20);">
                            <i class="bi bi-filetype-json fs-4 text-danger"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Option 3: JSON Export</h5>
                            <p class="text-muted small mb-0">All records in a single JSON file</p>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">
                        Generates <code>database_backup.json</code> containing every record
                        organized by table. Ideal for programmatic data processing or restoration.
                    </p>
                    <a href="{{ route('admin.database.export.json') }}" class="btn btn-danger w-100"
                       onclick="return confirm('Download JSON export of entire database?')">
                        <i class="bi bi-download me-2"></i> Download JSON Export
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card stat-card h-100 border border-warning border-opacity-25">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #fbbf2420, #f59e0b20);">
                            <i class="bi bi-archive fs-4 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Full Backup (All)</h5>
                            <p class="text-muted small mb-0">Complete backup in a single ZIP archive</p>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">
                        Creates a ZIP containing the SQL dump, all CSV files, JSON backup,
                        and the raw SQLite database file. Best for safe migration preparation.
                    </p>
                    <a href="{{ route('admin.database.backup') }}" class="btn btn-warning w-100"
                       onclick="return confirm('Create full database backup ZIP? This may take a moment.')">
                        <i class="bi bi-shield-check me-2"></i> Create Full Backup
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="card stat-card">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill fs-4 text-primary flex-shrink-0 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-2">About Database Exports</h6>
                    <p class="small text-muted mb-1">
                        All exports are generated from your current SQLite database. These files can be used to:
                    </p>
                    <ul class="small text-muted mb-0">
                        <li>Migrate to MySQL (see <strong>USEDMYSQL.md</strong> in the project root for the migration guide)</li>
                        <li>Create backups before major changes</li>
                        <li>Import into other database tools</li>
                        <li>Share data without sharing the database file</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards on load
    document.querySelectorAll('.stat-card-premium').forEach(function(card, i) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(function() {
            card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * i);
    });
});
</script>
@endpush
