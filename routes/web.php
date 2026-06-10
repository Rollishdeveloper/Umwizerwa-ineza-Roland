<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\FinalExamController;
use App\Http\Controllers\AICourseGeneratorController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\InstructorManagementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DatabaseExportController;
use App\Http\Controllers\Admin\DatabaseImportController;
use App\Http\Controllers\Instructor\InstructorController;
use App\Http\Controllers\Student\StudentController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Course Routes (guests can view courses)
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show')->whereNumber('course');

// Authentication Routes (Breeze)
require __DIR__ . '/auth.php';

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    // Default Dashboard (redirects based on role)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    })->name('dashboard');

    // Gamification Routes
    Route::prefix('gamification')->name('gamification.')->group(function () {
        Route::get('/', [GamificationController::class, 'index'])->name('index');
        Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/badges', [GamificationController::class, 'badges'])->name('badges');
        Route::get('/achievements', [GamificationController::class, 'achievements'])->name('achievements');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Final Exam Routes
    Route::prefix('courses/{course}/final-exam')->name('final-exams.')->group(function () {
        Route::get('/', [FinalExamController::class, 'show'])->name('show');
        Route::get('/take/{exam}', [FinalExamController::class, 'take'])->name('take');
        Route::post('/submit/{exam}', [FinalExamController::class, 'submit'])->name('submit');
        Route::get('/result/{exam}/{result}', [FinalExamController::class, 'result'])->name('result');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('markRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
    });

    // Certificate Routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/verify', [CertificateController::class, 'verify'])->name('verify');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::post('/generate/{enrollment}', [CertificateController::class, 'generate'])->name('generate');
    });

    // Course Routes
    Route::resource('courses', CourseController::class)->except(['show']);
    Route::post('courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');

        // AI Course Generator Routes
    Route::prefix('ai-generator')->name('ai-generator.')->group(function () {
        Route::get('/', [AICourseGeneratorController::class, 'index'])->name('index');
        Route::get('/upload', [AICourseGeneratorController::class, 'uploadForm'])->name('upload');
        Route::post('/upload', [AICourseGeneratorController::class, 'upload'])->name('upload.post');
        Route::get('/preview/{material}', [AICourseGeneratorController::class, 'preview'])->name('preview');
        Route::post('/generate/{material}', [AICourseGeneratorController::class, 'generate'])->name('generate');
        Route::get('/history', [AICourseGeneratorController::class, 'history'])->name('history');
        Route::post('/generate-video-script/{material}', [AICourseGeneratorController::class, 'generateVideoScript'])->name('video-script');
        Route::post('/generate-presentation/{material}', [AICourseGeneratorController::class, 'generatePresentation'])->name('presentation');
        Route::post('/combine-materials', [AICourseGeneratorController::class, 'combineMaterials'])->name('combine');
    });

    // Approval Workflow Routes
    Route::prefix('approval')->name('approval.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/dashboard', [ApprovalController::class, 'dashboard'])->name('dashboard');
        Route::get('/queue', [ApprovalController::class, 'queue'])->name('queue');
        Route::get('/review/{course}', [ApprovalController::class, 'review'])->name('review');
        Route::post('/review/{course}/submit', [ApprovalController::class, 'submitReview'])->name('submit-review');
        Route::get('/versions/{course}', [ApprovalController::class, 'versions'])->name('versions');
        Route::get('/analytics', [ApprovalController::class, 'analytics'])->name('analytics');
    });

    // Question Bank Routes
    Route::prefix('question-bank')->name('question-bank.')->group(function () {
        Route::get('/', [QuestionBankController::class, 'index'])->name('index');
        Route::get('/create', [QuestionBankController::class, 'create'])->name('create');
        Route::post('/', [QuestionBankController::class, 'store'])->name('store');
        Route::get('/{question}', [QuestionBankController::class, 'show'])->name('show');
        Route::get('/{question}/edit', [QuestionBankController::class, 'edit'])->name('edit');
        Route::put('/{question}', [QuestionBankController::class, 'update'])->name('update');
        Route::delete('/{question}', [QuestionBankController::class, 'destroy'])->name('destroy');
        Route::post('/{question}/approve', [QuestionBankController::class, 'approve'])->name('approve');
        Route::post('/generate/{course}', [QuestionBankController::class, 'generateForCourse'])->name('generate');
    });

    // Enrollment Routes
    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::post('/{enrollment}/progress', [EnrollmentController::class, 'updateProgress'])->name('progress');
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('destroy');
    });

    // Module Routes
    Route::post('courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
    Route::put('modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

    // Lesson Routes
    Route::post('modules/{module}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    // Quiz Routes (nested under courses)
    Route::prefix('courses/{course}/quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index');
        Route::get('/create', [QuizController::class, 'create'])->name('create');
        Route::post('/', [QuizController::class, 'store'])->name('store');
        Route::get('/{quiz}', [QuizController::class, 'show'])->name('show');
        Route::get('/{quiz}/edit', [QuizController::class, 'edit'])->name('edit');
        Route::put('/{quiz}', [QuizController::class, 'update'])->name('update');
        Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('destroy');
        Route::get('/{quiz}/take', [QuizController::class, 'takeQuiz'])->name('take');
        Route::post('/{quiz}/submit', [QuizController::class, 'submitQuiz'])->name('submit');
        Route::get('/{quiz}/result/{result}', [QuizController::class, 'result'])->name('result');
        Route::post('/{quiz}/questions', [QuizController::class, 'addQuestion'])->name('questions.store');
        Route::put('/{quiz}/questions/{question}', [QuizController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/{quiz}/questions/{question}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');
    });

    // Assignment Routes (nested under courses)
    Route::prefix('courses/{course}/assignments')->name('assignments.')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('index');
        Route::post('/', [AssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}', [AssignmentController::class, 'show'])->name('show');
        Route::put('/{assignment}', [AssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [AssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/{assignment}/submit', [AssignmentController::class, 'submit'])->name('submit');
        Route::post('/{assignment}/grade/{submission}', [AssignmentController::class, 'grade'])->name('grade');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.updateStatus');
    Route::match(['get', 'post'], '/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/students', [StudentManagementController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentManagementController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentManagementController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentManagementController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentManagementController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentManagementController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentManagementController::class, 'destroy'])->name('students.destroy');
    Route::get('/instructors', [InstructorManagementController::class, 'index'])->name('instructors.index');
    Route::get('/instructors/create', [InstructorManagementController::class, 'create'])->name('instructors.create');
    Route::post('/instructors', [InstructorManagementController::class, 'store'])->name('instructors.store');
    Route::get('/instructors/{instructor}', [InstructorManagementController::class, 'show'])->name('instructors.show');
    Route::get('/instructors/{instructor}/edit', [InstructorManagementController::class, 'edit'])->name('instructors.edit');
    Route::put('/instructors/{instructor}', [InstructorManagementController::class, 'update'])->name('instructors.update');
    Route::delete('/instructors/{instructor}', [InstructorManagementController::class, 'destroy'])->name('instructors.destroy');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Database Tools Routes
    Route::prefix('database')->name('database.')->group(function () {
        // Export
        Route::get('/export', [DatabaseExportController::class, 'index'])->name('export');
        Route::get('/export/sql', [DatabaseExportController::class, 'exportSql'])->name('export.sql');
        Route::get('/export/csv', [DatabaseExportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/json', [DatabaseExportController::class, 'exportJson'])->name('export.json');
        Route::get('/export/backup', [DatabaseExportController::class, 'backup'])->name('backup');

        // Import
        Route::get('/import', [DatabaseImportController::class, 'index'])->name('import');
        Route::post('/import/migrate', [DatabaseImportController::class, 'runMigrations'])->name('import.migrate');
        Route::post('/import/validate', [DatabaseImportController::class, 'validate'])->name('import.validate');
        Route::post('/import/preflight', [DatabaseImportController::class, 'preflightCheck'])->name('import.preflight');
    });
});

// Instructor Routes
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/enrollments', [InstructorController::class, 'enrollments'])->name('enrollments');
    Route::get('/enrollments/add', [InstructorController::class, 'addEnrollmentForm'])->name('add-enrollment');
    Route::post('/enrollments', [InstructorController::class, 'storeEnrollment'])->name('store-enrollment');
    Route::delete('/enrollments/{enrollment}', [InstructorController::class, 'destroyEnrollment'])->name('destroy-enrollment');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
});

// Report Routes
Route::middleware(['auth', 'role:admin'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/students', [ReportController::class, 'students'])->name('students');
    Route::get('/courses', [ReportController::class, 'courses'])->name('courses');
    Route::get('/instructors', [ReportController::class, 'instructors'])->name('instructors');
    Route::get('/system', [ReportController::class, 'system'])->name('system');
    Route::get('/export-pdf/{type}', [ReportController::class, 'exportPdf'])->name('export-pdf');
    Route::get('/export-csv/{type}', [ReportController::class, 'exportCsv'])->name('export-csv');
    Route::get('/export-excel/{type}', [ReportController::class, 'exportExcel'])->name('export-excel');
});
