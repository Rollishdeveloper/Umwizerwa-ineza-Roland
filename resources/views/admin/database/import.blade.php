@extends('layouts.app')

@section('title', 'Import to MySQL')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-cloud-upload me-2 text-success"></i> Import to MySQL</h4>
            <p class="text-muted mb-0">Migrate your SQLite database to MySQL for production</p>
        </div>
        <div>
            <a href="{{ route('admin.database.export') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-database"></i> Export Database
            </a>
        </div>
    </div>

    {{-- Migration Steps --}}
    <div class="row g-4">
        {{-- Step 1: Configuration Status --}}
        <div class="col-lg-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 32px; height: 32px;">1</span>
                        <h6 class="fw-bold mb-0">MySQL Configuration</h6>
                    </div>

                    @if($mysqlConnected)
                        <div class="alert alert-success py-2 small mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Connected to MySQL
                        </div>
                    @elseif($mysqlAvailable)
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Not connected
                            @if($mysqlError)
                                <br><small class="text-danger">{{ $mysqlError }}</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="bi bi-x-circle-fill me-1"></i> pdo_mysql extension not loaded
                        </div>
                    @endif

                    <p class="small text-muted mb-2">To configure MySQL, update your <code>.env</code> file:</p>
                    <pre class="small bg-light rounded p-2 mb-0" style="font-size: 0.75rem;">
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elearning_db
DB_USERNAME=root
DB_PASSWORD=</pre>

                    <a href="{{ route('admin.settings') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-gear"></i> Open Settings
                    </a>
                </div>
            </div>
        </div>

        {{-- Step 2: Migration Actions --}}
        <div class="col-lg-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 32px; height: 32px;">2</span>
                        <h6 class="fw-bold mb-0">Run Migration</h6>
                    </div>

                    <p class="small text-muted mb-3">
                        Execute all Laravel migrations on the MySQL database. 
                        A full backup of your SQLite data will be created automatically first.
                    </p>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" id="runMigrationsBtn"
                                onclick="runMigrations()"
                                @if(!$mysqlConnected) disabled @endif>
                            <i class="bi bi-arrow-right-circle me-2"></i> Run Migrations on MySQL
                        </button>

                        <button type="button" class="btn btn-outline-info" id="preflightBtn"
                                onclick="runPreflight()"
                                @if(!$mysqlConnected) disabled @endif>
                            <i class="bi bi-search me-2"></i> Preflight Check
                        </button>
                    </div>

                    <div id="migrationSpinner" class="text-center mt-3 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Running...</span>
                        </div>
                        <p class="small text-muted mt-2 mb-0">Creating backup and running migrations...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Import Data --}}
        <div class="col-lg-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle p-2" style="width: 32px; height: 32px;">3</span>
                        <h6 class="fw-bold mb-0">Import Exported Data</h6>
                    </div>

                    <p class="small text-muted mb-3">
                        Upload a previously exported SQL or JSON file to validate and import into MySQL.
                    </p>

                    <form action="{{ route('admin.database.import.validate') }}" method="POST" enctype="multipart/form-data" id="validateForm">
                        @csrf
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" name="import_file"
                                   accept=".sql,.json,.txt" required
                                   @if(!$mysqlConnected) disabled @endif>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-success w-100"
                                @if(!$mysqlConnected) disabled @endif>
                            <i class="bi bi-check2-circle me-1"></i> Validate & Import
                        </button>
                    </form>

                    <div id="validationResult" class="mt-2 d-none"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preflight Results --}}
    <div id="preflightResults" class="mt-4 d-none">
        <div class="card stat-card">
            <div class="card-header bg-transparent border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-success"></i> Preflight Check Results</h6>
            </div>
            <div class="card-body" id="preflightBody">
            </div>
        </div>
    </div>

    {{-- Available Export Files --}}
    @if(count($exportFiles) > 0)
    <div class="card stat-card mt-4">
        <div class="card-header bg-transparent border-0">
            <h6 class="fw-bold mb-0"><i class="bi bi-files me-2"></i> Available Export Files</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>File</th>
                            <th>Size</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exportFiles as $file)
                            <tr>
                                <td><code>{{ $file['name'] }}</code></td>
                                <td>{{ $file['size'] }}</td>
                                <td>{{ $file['date'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Info Box --}}
    <div class="card stat-card mt-4">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-journal-text fs-4 text-success flex-shrink-0 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-2">Migration Workflow</h6>
                    <ol class="small text-muted mb-0 ps-3">
                        <li><strong>Configure</strong> MySQL in your <code>.env</code> file</li>
                        <li><strong>Export</strong> your SQLite data (SQL, CSV, or JSON)</li>
                        <li><strong>Run</strong> migrations on MySQL to create table structures</li>
                        <li><strong>Import</strong> your exported data into MySQL tables</li>
                        <li><strong>Test</strong> your application with <code>php artisan serve</code></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function runMigrations() {
    if (!confirm('This will run all migrations on MySQL.\n\nA full backup of your current SQLite database will be created automatically first.\n\nContinue?')) {
        return;
    }

    document.getElementById('migrationSpinner').classList.remove('d-none');
    document.getElementById('runMigrationsBtn').disabled = true;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.database.import.migrate') }}';
    form.innerHTML = '@csrf';
    document.body.appendChild(form);
    form.submit();
}

function runPreflight() {
    const btn = document.getElementById('preflightBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Checking...';

    fetch('{{ route('admin.database.import.preflight') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        const container = document.getElementById('preflightResults');
        const body = document.getElementById('preflightBody');
        container.classList.remove('d-none');

        body.innerHTML = data.results.map(r =>
            `<div class="d-flex align-items-center gap-3 py-2 border-bottom border-light">
                <span style="font-size: 1.25rem;">${r.status}</span>
                <div>
                    <strong class="small">${r.check}</strong>
                    <p class="small text-muted mb-0">${r.detail}</p>
                </div>
            </div>`
        ).join('');

        if (!data.passed) {
            container.querySelector('.card-header h6').innerHTML =
                '<i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i> Preflight Check Failed';
        }
    })
    .catch(err => {
        alert('Preflight check failed: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search me-2"></i> Preflight Check';
    });
}

document.getElementById('validateForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const result = document.getElementById('validationResult');
    const btn = form.querySelector('button[type="submit"]');
    const fileInput = form.querySelector('input[type="file"]');

    if (!fileInput.files.length) {
        result.className = 'alert alert-danger py-1 small mt-2';
        result.textContent = 'Please select a file.';
        result.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Validating...';

    const fd = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.valid) {
            result.className = 'alert alert-success py-1 small mt-2';
            result.innerHTML = '<i class="bi bi-check-circle me-1"></i> File is valid!';
        } else {
            result.className = 'alert alert-danger py-1 small mt-2';
            result.innerHTML = '<i class="bi bi-x-circle me-1"></i> Validation failed:<br>' +
                data.errors.map(e => '&bull; ' + e).join('<br>');
        }
        if (data.warnings && data.warnings.length) {
            result.innerHTML += '<br><small class="text-warning">' + data.warnings.map(w => '⚠ ' + w).join('<br>') + '</small>';
        }
        if (data.info && data.info.length) {
            result.innerHTML += '<br><small class="text-info">' + data.info.join('<br>') + '</small>';
        }
        result.classList.remove('d-none');
    })
    .catch(err => {
        result.className = 'alert alert-danger py-1 small mt-2';
        result.textContent = 'Error: ' + err.message;
        result.classList.remove('d-none');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Validate & Import';
    });
});
</script>
@endpush
