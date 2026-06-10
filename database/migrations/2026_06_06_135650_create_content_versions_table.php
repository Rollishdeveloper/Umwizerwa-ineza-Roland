<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_versions', function (Blueprint $table) {
            $table->id('version_id');
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->string('version_number');
            $table->text('changes')->nullable();
            $table->foreignId('created_by')->constrained('users', 'id')->onDelete('cascade');
            $table->enum('status', ['ai_generated', 'instructor_reviewed', 'coordinator_approved', 'published', 'archived'])->default('ai_generated');
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_versions');
    }
};
