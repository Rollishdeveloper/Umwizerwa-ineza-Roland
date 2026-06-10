<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseExportController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Show the database tools page (export options).
     */
    public function index()
    {
        $dbSize = 0;
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $dbSize = round(filesize($dbPath) / 1024, 1); // KB
        }

        $tableCount = 0;
        try {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tableCount = count($tables);
        } catch (\Exception $e) {
            $tableCount = 0;
        }

        return view('admin.database.export', compact('dbSize', 'tableCount'));
    }

    /**
     * Export all data as SQL INSERT statements.
     */
    public function exportSql()
    {
        $timestamp = now()->format('Y-m-d_Hi');
        $sqlPath = storage_path("exports/sqlite_export_{$timestamp}.sql");

        if (!is_dir(storage_path('exports'))) {
            mkdir(storage_path('exports'), 0755, true);
        }

        $this->backupService->exportSql($sqlPath);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'activity' => 'Exported database as SQL file: sqlite_export_' . $timestamp . '.sql',
        ]);

        return response()->download($sqlPath)->deleteFileAfterSend(true);
    }

    /**
     * Export all tables as a ZIP of CSV files.
     */
    public function exportCsv()
    {
        $timestamp = now()->format('Y-m-d_Hi');
        $csvDir = storage_path("exports/csv_{$timestamp}");

        if (!is_dir($csvDir)) {
            mkdir($csvDir, 0755, true);
        }

        $this->backupService->exportAllCsv($csvDir);

        // Zip the CSV directory
        $zipPath = storage_path("exports/csv_export_{$timestamp}.zip");
        $this->backupService->createZip($csvDir, $zipPath);

        // Cleanup
        $this->rmdirRecursive($csvDir);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'activity' => 'Exported database as CSV zip: csv_export_' . $timestamp . '.zip',
        ]);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Export all data as a single JSON file.
     */
    public function exportJson()
    {
        $timestamp = now()->format('Y-m-d_Hi');
        $jsonPath = storage_path("exports/database_backup_{$timestamp}.json");

        if (!is_dir(storage_path('exports'))) {
            mkdir(storage_path('exports'), 0755, true);
        }

        $this->backupService->exportJson($jsonPath);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'activity' => 'Exported database as JSON: database_backup_' . $timestamp . '.json',
        ]);

        return response()->download($jsonPath)->deleteFileAfterSend(true);
    }

    /**
     * Create a full backup zip (SQL + CSV + JSON + raw SQLite).
     */
    public function backup()
    {
        $backupPath = $this->backupService->createFullBackup();

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'activity' => 'Created full database backup: ' . basename($backupPath),
        ]);

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }

    protected function rmdirRecursive(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
}
