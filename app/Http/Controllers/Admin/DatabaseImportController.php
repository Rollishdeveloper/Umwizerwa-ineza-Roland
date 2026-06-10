<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseImportController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Show the MySQL import page.
     */
    public function index()
    {
        $mysqlAvailable = extension_loaded('pdo_mysql');
        $currentDriver = DB::connection()->getDriverName();

        // Check if MySQL config looks valid
        $mysqlConfig = config('database.connections.mysql');
        $mysqlReady = $mysqlAvailable
            && !empty($mysqlConfig['host'])
            && !empty($mysqlConfig['database']);

        // Test MySQL connection
        $mysqlConnected = false;
        $mysqlError = null;
        if ($mysqlReady) {
            try {
                DB::connection('mysql')->getPdo();
                $mysqlConnected = true;
            } catch (\Exception $e) {
                $mysqlError = $e->getMessage();
            }
        }

        // List available export files
        $exportFiles = $this->getExportFiles();

        return view('admin.database.import', compact(
            'mysqlAvailable',
            'currentDriver',
            'mysqlReady',
            'mysqlConnected',
            'mysqlError',
            'exportFiles'
        ));
    }

    /**
     * Run migrations on MySQL connection.
     */
    public function runMigrations()
    {
        try {
            // Automatically create backup first
            $backupPath = $this->backupService->createFullBackup();

            Artisan::call('migrate', ['--database' => 'mysql', '--force' => true]);
            $output = Artisan::output();

            ActivityLog::create([
                'user_id'  => auth()->id(),
                'activity' => 'Ran migrations on MySQL database. Backup created: ' . basename($backupPath),
            ]);

            return back()->with('success', 'Migrations executed successfully on MySQL!<br><small>Backup saved: ' . basename($backupPath) . '</small>');
        } catch (\Exception $e) {
            ActivityLog::create([
                'user_id'  => auth()->id(),
                'activity' => 'MySQL migration failed: ' . $e->getMessage(),
            ]);

            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate an uploaded SQL/JSON file before import.
     */
    public function validate(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:sql,json,txt|max:102400',
        ]);

        $file = $request->file('import_file');
        $content = file_get_contents($file->getRealPath());
        $errors = [];
        $warnings = [];
        $info = [];

        $extension = $file->getClientOriginalExtension();

        if ($extension === 'json') {
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'Invalid JSON: ' . json_last_error_msg();
            } else {
                $info[] = 'JSON is valid. Tables found: ' . implode(', ', array_keys($data));
                // Check for expected tables
                $expectedTables = ['users', 'students', 'courses', 'enrollments'];
                foreach ($expectedTables as $table) {
                    if (!isset($data[$table])) {
                        $warnings[] = "Missing expected table: {$table}";
                    }
                }
            }
        } elseif ($extension === 'sql') {
            // Basic SQL validation
            if (!preg_match('/INSERT\s+INTO/i', $content) && !preg_match('/CREATE\s+TABLE/i', $content)) {
                $warnings[] = 'SQL file appears to have no INSERT or CREATE TABLE statements.';
            } else {
                $insertCount = preg_match_all('/INSERT\s+INTO/i', $content, $matches);
                $info[] = "Found approximately {$insertCount} INSERT statements.";
            }
        }

        return response()->json([
            'valid'    => empty($errors),
            'errors'   => $errors,
            'warnings' => $warnings,
            'info'     => $info,
        ]);
    }

    /**
     * Run a pre-migration check against the MySQL connection.
     */
    public function preflightCheck()
    {
        $results = [];
        $allPassed = true;

        try {
            $mysql = DB::connection('mysql');

            // Check connection
            $mysql->getPdo();
            $results[] = ['check' => 'Connection', 'status' => '✅', 'detail' => 'Connected to MySQL successfully'];

            // Check charset
            $charset = $mysql->select("SELECT @@character_set_database AS charset")[0]->charset ?? 'unknown';
            $results[] = ['check' => 'Charset', 'status' => '✅', 'detail' => "Database charset: {$charset}"];

            // Check tables exist (if migration already ran)
            $tables = $mysql->select("SHOW TABLES");
            if (count($tables) > 0) {
                $tableNames = array_map(fn($t) => current((array) $t), $tables);
                $results[] = ['check' => 'Tables', 'status' => 'ℹ️', 'detail' => 'Tables already exist: ' . implode(', ', $tableNames)];
            } else {
                $results[] = ['check' => 'Tables', 'status' => '✅', 'detail' => 'No existing tables — clean slate ready for migration'];
            }

            // Check foreign key support
            $fkCheck = $mysql->select("SELECT @@foreign_key_checks AS fk")[0]->fk ?? 1;
            $results[] = ['check' => 'Foreign Keys', 'status' => '✅', 'detail' => 'Foreign key checks: ' . ($fkCheck ? 'ON' : 'OFF')];

        } catch (\Exception $e) {
            $results[] = ['check' => 'Connection', 'status' => '❌', 'detail' => $e->getMessage()];
            $allPassed = false;
        }

        return response()->json([
            'passed' => $allPassed,
            'results' => $results,
        ]);
    }

    /**
     * Get list of available export files in storage.
     */
    protected function getExportFiles(): array
    {
        $files = [];
        $exportDir = storage_path('exports');

        if (!is_dir($exportDir)) {
            return $files;
        }

        $pattern = $exportDir . '/*.{sql,json,zip}';
        foreach (glob($pattern, GLOB_BRACE) as $path) {
            $files[] = [
                'name' => basename($path),
                'size' => round(filesize($path) / 1024, 1) . ' KB',
                'date' => date('Y-m-d H:i', filemtime($path)),
            ];
        }

        // Sort by date descending
        usort($files, fn($a, $b) => strcmp($b['date'], $a['date']));

        return $files;
    }
}
