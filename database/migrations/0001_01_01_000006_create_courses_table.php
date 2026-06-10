<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->foreignId('instructor_id')->constrained('instructors', 'instructor_id')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->integer('duration')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all'])->default('beginner');
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
