<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Category;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizResult;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\Badge;
use App\Models\Achievement;
use App\Models\UploadedMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    protected array $models = [
        'users'                  => User::class,
        'students'               => Student::class,
        'instructors'            => Instructor::class,
        'categories'             => Category::class,
        'courses'                => Course::class,
        'modules'                => Module::class,
        'lessons'                => Lesson::class,
        'enrollments'            => Enrollment::class,
        'quizzes'                => Quiz::class,
        'questions'              => Question::class,
        'quiz_results'           => QuizResult::class,
        'assignments'            => Assignment::class,
        'assignment_submissions' => AssignmentSubmission::class,
        'certificates'           => Certificate::class,
        'notifications'          => Notification::class,
        'activity_logs'          => ActivityLog::class,
        'badges'                 => Badge::class,
        'achievements'           => Achievement::class,
        'uploaded_materials'     => UploadedMaterial::class,
    ];

    /**
     * Create a full backup: SQL dump + CSV exports + JSON export + raw SQLite file.
     * Returns the path to the generated zip archive.
     */
    public function createFullBackup(): string
    {
        $timestamp = now()->format('Y-m-d_Hi');
        $backupDir = storage_path("backups/backup_{$timestamp}");
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // 1. JSON export (all tables)
        $jsonPath = "{$backupDir}/database_backup.json";
        $this->exportJson($jsonPath);

        // 2. CSV exports (one per table)
        $csvDir = "{$backupDir}/csv";
        if (!is_dir($csvDir)) {
            mkdir($csvDir, 0755, true);
        }
        $this->exportAllCsv($csvDir);

        // 3. SQL export
        $sqlPath = "{$backupDir}/sqlite_export.sql";
        $this->exportSql($sqlPath);

        // 4. Copy raw SQLite file
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            copy($dbPath, "{$backupDir}/database.sqlite");
        }

        // 5. Zip everything
        $zipPath = storage_path("backups/database_backup_{$timestamp}.zip");
        $this->createZip($backupDir, $zipPath);

        // Cleanup the temp directory
        $this->rmdirRecursive($backupDir);

        return $zipPath;
    }

    /**
     * Export all tables to a single JSON file.
     */
    public function exportJson(string $outputPath): void
    {
        $data = [];
        foreach ($this->models as $table => $modelClass) {
            $data[$table] = $modelClass::all()->toArray();
        }

        file_put_contents(
            $outputPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Export each table to a separate CSV file.
     */
    public function exportAllCsv(string $csvDir): void
    {
        foreach ($this->models as $table => $modelClass) {
            $records = $modelClass::all()->toArray();
            $csvPath = "{$csvDir}/{$table}.csv";

            $handle = fopen($csvPath, 'w');
            if (empty($records)) {
                fputcsv($handle, ['no_data']);
                fclose($handle);
                continue;
            }

            // Header row
            fputcsv($handle, array_keys($records[0]));

            // Data rows
            foreach ($records as $record) {
                $encoded = [];
                foreach ($record as $key => $value) {
                    // Handle nulls and arrays
                    if (is_null($value)) {
                        $encoded[$key] = '';
                    } elseif (is_array($value) || is_object($value)) {
                        $encoded[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                    } else {
                        $encoded[$key] = (string) $value;
                    }
                }
                fputcsv($handle, $encoded);
            }
            fclose($handle);
        }

        // Also export pivot tables
        $this->exportPivotCsv($csvDir);
    }

    /**
     * Export pivot tables (many-to-many).
     */
    protected function exportPivotCsv(string $csvDir): void
    {
        $pivotTables = [
            'student_badges',
            'course_tag',
        ];

        foreach ($pivotTables as $table) {
            try {
                $records = DB::table($table)->get()->toArray();
                $csvPath = "{$csvDir}/{$table}.csv";
                $handle = fopen($csvPath, 'w');

                if (empty($records)) {
                    fputcsv($handle, ['no_data']);
                    fclose($handle);
                    continue;
                }

                fputcsv($handle, array_keys((array) $records[0]));
                foreach ($records as $record) {
                    fputcsv($handle, (array) $record);
                }
                fclose($handle);
            } catch (\Exception $e) {
                // Pivot table may not exist — skip silently
            }
        }
    }

    /**
     * Export all tables as SQL INSERT statements.
     */
    public function exportSql(string $outputPath): void
    {
        $lines = [];
        $lines[] = '-- ===================================================';
        $lines[] = '-- E-LMS Database Export (SQLite Compatible)';
        $lines[] = '-- Generated: ' . now()->format('Y-m-d H:i:s');
        $lines[] = '-- ===================================================';
        $lines[] = '';

        foreach ($this->models as $table => $modelClass) {
            $records = $modelClass::all()->toArray();
            if (empty($records)) {
                continue;
            }

            $lines[] = "-- Table: {$table}";
            $lines[] = "DELETE FROM `{$table}`;";

            foreach ($records as $record) {
                $columns = array_keys($record);
                $values = array_map(function ($val) {
                    if (is_null($val)) {
                        return 'NULL';
                    }
                    if (is_array($val) || is_object($val)) {
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                    }
                    return "'" . str_replace("'", "''", (string) $val) . "'";
                }, array_values($record));

                $lines[] = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");";
            }
            $lines[] = '';
        }

        // Export pivot tables
        $pivotTables = ['student_badges', 'course_tag'];
        foreach ($pivotTables as $table) {
            try {
                $records = DB::table($table)->get()->toArray();
                if (empty($records)) {
                    continue;
                }
                $lines[] = "-- Pivot table: {$table}";
                $lines[] = "DELETE FROM `{$table}`;";
                foreach ($records as $record) {
                    $record = (array) $record;
                    $columns = array_keys($record);
                    $values = array_map(function ($val) {
                        return is_null($val) ? 'NULL' : "'" . str_replace("'", "''", (string) $val) . "'";
                    }, array_values($record));
                    $lines[] = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");";
                }
                $lines[] = '';
            } catch (\Exception $e) {
                // Skip if table doesn't exist
            }
        }

        file_put_contents($outputPath, implode("\n", $lines));
    }

    /**
     * Create a zip archive from a directory.
     */
    protected function createZip(string $sourceDir, string $outputPath): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($outputPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip at {$outputPath}");
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $relativePath = substr($file->getRealPath(), strlen($sourceDir) + 1);
            $zip->addFile($file->getRealPath(), $relativePath);
        }

        $zip->close();
    }

    /**
     * Recursively delete a directory.
     */
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
