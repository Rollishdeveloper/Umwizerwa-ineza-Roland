<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Changes correct_answer from enum('a','b','c','d') to string(500)
     * so the AI generator can store text answers for fill_blank/short_answer questions.
     * Also makes option columns nullable for non-MCQ questions.
     */
    public function up(): void
    {
        // SQLite doesn't support altering columns, so we recreate the table
        Schema::create('final_exam_questions_temp', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('exam_id')->constrained('final_exams', 'exam_id')->onDelete('cascade');
            $table->text('question_text');
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();
            $table->text('correct_answer');
            $table->timestamps();
        });

        // Copy existing data
        DB::statement('INSERT INTO final_exam_questions_temp (question_id, exam_id, question_text, option_a, option_b, option_c, option_d, correct_answer, created_at, updated_at)
                        SELECT question_id, exam_id, question_text, option_a, option_b, option_c, option_d, correct_answer, created_at, updated_at
                        FROM final_exam_questions');

        // Drop old table and rename temp
        Schema::dropIfExists('final_exam_questions');
        Schema::rename('final_exam_questions_temp', 'final_exam_questions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('final_exam_questions_temp', function (Blueprint $table) {
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

        // Only copy rows where correct_answer is in a,b,c,d to avoid data loss
        DB::statement('INSERT INTO final_exam_questions_temp (question_id, exam_id, question_text, option_a, option_b, option_c, option_d, correct_answer, created_at, updated_at)
                        SELECT question_id, exam_id, question_text, COALESCE(option_a, \'\'), COALESCE(option_b, \'\'), COALESCE(option_c, \'\'), COALESCE(option_d, \'\'), correct_answer, created_at, updated_at
                        FROM final_exam_questions
                        WHERE correct_answer IN (\'a\', \'b\', \'c\', \'d\')');

        Schema::dropIfExists('final_exam_questions');
        Schema::rename('final_exam_questions_temp', 'final_exam_questions');
    }
};
