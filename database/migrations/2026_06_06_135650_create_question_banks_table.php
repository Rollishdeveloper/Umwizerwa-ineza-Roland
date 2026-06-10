<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('course_id')->nullable()->constrained('courses', 'course_id')->nullOnDelete();
            $table->foreignId('module_id')->nullable()->constrained('modules', 'module_id')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'])->default('mcq');
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->string('topic')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 8, 2)->default(10);
            $table->string('source_reference')->nullable();
            $table->string('source_page')->nullable();
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
