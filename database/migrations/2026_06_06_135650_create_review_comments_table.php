<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->foreignId('review_id')->constrained('course_reviews', 'review_id')->onDelete('cascade');
            $table->enum('content_type', ['course', 'module', 'lesson', 'quiz', 'question', 'assignment', 'exam'])->default('course');
            $table->unsignedBigInteger('content_id')->nullable();
            $table->text('comment');
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_comments');
    }
};
