<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id('badge_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#667eea');
            $table->enum('type', ['points', 'courses', 'quizzes', 'assignments', 'streak', 'certificates', 'custom'])->default('custom');
            $table->integer('required_points')->nullable();
            $table->integer('required_count')->nullable();
            $table->timestamps();
        });

        Schema::create('achievements', function (Blueprint $table) {
            $table->id('achievement_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#28a745');
            $table->enum('type', ['first_course', 'quiz_star', 'assignment_pro', 'course_master', 'points_milestone', 'certificate_collector', 'streak', 'social', 'custom'])->default('custom');
            $table->integer('required_value')->default(1);
            $table->timestamps();
        });

        Schema::create('student_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->foreignId('badge_id')->constrained('badges', 'badge_id')->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent();
            $table->unique(['student_id', 'badge_id']);
        });

        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained('achievements', 'achievement_id')->onDelete('cascade');
            $table->timestamp('unlocked_at')->useCurrent();
            $table->unique(['student_id', 'achievement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_achievements');
        Schema::dropIfExists('student_badges');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('badges');
    }
};
