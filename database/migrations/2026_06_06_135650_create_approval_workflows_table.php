<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id('workflow_id');
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->enum('current_stage', ['uploaded', 'ai_generated', 'pending_review', 'instructor_review', 'coordinator_review', 'admin_approval', 'published', 'rejected', 'archived'])->default('uploaded');
            $table->foreignId('assigned_reviewer')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->text('suggested_corrections')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_workflows');
    }
};
