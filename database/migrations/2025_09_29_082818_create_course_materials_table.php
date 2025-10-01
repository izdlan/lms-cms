<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('class_code');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('material_type', ['document', 'video', 'image', 'audio', 'link', 'other']);
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('file_name')->nullable(); // Original filename
            $table->string('file_size')->nullable(); // File size in bytes
            $table->string('file_extension')->nullable(); // File extension
            $table->string('external_url')->nullable(); // For external links
            $table->string('author_name');
            $table->string('author_email');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true); // Whether students can see it
            $table->integer('download_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['subject_code', 'class_code']);
            $table->index(['material_type']);
            $table->index(['is_active', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
