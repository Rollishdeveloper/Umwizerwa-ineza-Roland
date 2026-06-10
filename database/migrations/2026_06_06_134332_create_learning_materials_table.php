<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->morphs('materialable'); // attaches to lessons, modules, or courses
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'pdf', 'word', 'powerpoint', 'video', 'audio', 'image', 'external_link', 'other'
            ])->default('other');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};
