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
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('class_code');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size')->nullable();
            $table->string('uploaded_by_name');
            $table->string('uploaded_by_email');
            $table->boolean('is_active')->default(true);
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->foreign('subject_code')->references('code')->on('subjects');
            $table->index(['subject_code', 'class_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_contents');
    }
};
