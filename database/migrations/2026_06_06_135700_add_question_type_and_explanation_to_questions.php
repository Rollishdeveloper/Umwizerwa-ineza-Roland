<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('question_type', ['mcq', 'true_false', 'fill_blank', 'matching', 'short_answer', 'essay'])->default('mcq')->after('question_text');
            $table->text('explanation')->nullable()->after('correct_answer');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->after('explanation');
            $table->decimal('marks', 8, 2)->default(10)->after('difficulty');
            $table->decimal('ai_confidence', 5, 2)->nullable()->after('marks');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['question_type', 'explanation', 'difficulty', 'marks', 'ai_confidence']);
        });
    }
};
