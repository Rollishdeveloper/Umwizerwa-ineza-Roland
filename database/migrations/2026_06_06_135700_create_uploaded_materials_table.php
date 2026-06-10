<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploaded_materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses', 'course_id')->nullOnDelete();
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('mime_type');
            $table->integer('file_size')->nullable();
            $table->enum('status', ['uploaded', 'processing', 'processed', 'failed'])->default('uploaded');
            $table->text('extracted_text')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploaded_materials');
    }
};
