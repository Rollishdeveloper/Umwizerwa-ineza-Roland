<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN, so we rebuild the table
        Schema::create('questions_temp', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('quiz_id')->constrained('quizzes', 'quiz_id')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'])->default('mcq');
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 8, 2)->default(10);
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->timestamps();
        });

        // Copy all data from questions to temp
        DB::statement('INSERT INTO questions_temp 
            (question_id, quiz_id, question_text, question_type, option_a, option_b, option_c, option_d, 
             correct_answer, explanation, difficulty, marks, ai_confidence, created_at, updated_at)
            SELECT q.question_id, q.quiz_id, q.question_text, 
                   COALESCE(q.question_type, \'mcq\') as question_type,
                   q.option_a, q.option_b, q.option_c, q.option_d,
                   q.correct_answer, q.explanation, 
                   COALESCE(q.difficulty, \'medium\') as difficulty,
                   COALESCE(q.marks, 10) as marks,
                   q.ai_confidence, q.created_at, q.updated_at
            FROM questions q');

        // Drop old table and rename temp
        Schema::dropIfExists('questions');
        Schema::rename('questions_temp', 'questions');
    }

    public function down(): void
    {
        // Restore original enum constraint
        Schema::create('questions_old', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('quiz_id')->constrained('quizzes', 'quiz_id')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'])->default('mcq');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->enum('correct_answer', ['a', 'b', 'c', 'd']);
            $table->text('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 8, 2)->default(10);
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->timestamps();
        });

        // Copy only rows where correct_answer fits the enum constraint
        DB::statement('INSERT INTO questions_old 
            (question_id, quiz_id, question_text, question_type, option_a, option_b, option_c, option_d,
             correct_answer, explanation, difficulty, marks, ai_confidence, created_at, updated_at)
            SELECT question_id, quiz_id, question_text, question_type, option_a, option_b, option_c, option_d,
                   correct_answer, explanation, difficulty, marks, ai_confidence, created_at, updated_at
            FROM questions
            WHERE correct_answer IN (\'a\', \'b\', \'c\', \'d\')');

        Schema::dropIfExists('questions');
        Schema::rename('questions_old', 'questions');
    }
};
