<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_exams', function (Blueprint $table) {
            $table->id('exam_id');
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('passing_marks', 8, 2)->default(50);
            $table->integer('duration_minutes')->nullable();
            $table->integer('num_questions')->default(10);
            $table->boolean('auto_grade')->default(true);
            $table->integer('attempts_allowed')->default(1);
            $table->timestamps();
        });

        Schema::create('final_exam_questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('exam_id')->constrained('final_exams', 'exam_id')->onDelete('cascade');
            $table->text('question_text');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->enum('correct_answer', ['a', 'b', 'c', 'd']);
            $table->timestamps();
        });

        Schema::create('final_exam_results', function (Blueprint $table) {
            $table->id('result_id');
            $table->foreignId('exam_id')->constrained('final_exams', 'exam_id')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->enum('status', ['passed', 'failed', 'pending'])->default('pending');
            $table->integer('attempt')->default(1);
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id', 'attempt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_exam_results');
        Schema::dropIfExists('final_exam_questions');
        Schema::dropIfExists('final_exams');
    }
};
