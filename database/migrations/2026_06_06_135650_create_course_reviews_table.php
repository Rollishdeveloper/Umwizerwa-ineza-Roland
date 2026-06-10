<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users', 'id')->onDelete('cascade');
            $table->enum('review_type', ['instructor', 'coordinator', 'admin'])->default('instructor');
            $table->text('comments')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_required'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
    }
};
