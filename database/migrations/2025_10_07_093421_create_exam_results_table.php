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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject_code');
            $table->string('academic_year');
            $table->string('semester');
            $table->string('class_code')->nullable();
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null');
            
            // Student information (auto-filled)
            $table->string('student_name');
            $table->string('student_ic');
            $table->string('student_id')->nullable();
            
            // Assessment components (flexible JSON structure)
            $table->json('assessments')->nullable(); // Store all assessment types and scores
            
            // Calculated fields
            $table->decimal('total_marks', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable(); // A+, A, A-, B+, B, B-, C+, C, C-, D+, D, F
            $table->decimal('gpa', 3, 2)->nullable();
            
            // Status and metadata
            $table->enum('status', ['draft', 'published', 'finalized'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'subject_code', 'academic_year', 'semester']);
            $table->index(['subject_code', 'academic_year', 'semester']);
            $table->index(['lecturer_id', 'status']);
            $table->unique(['user_id', 'subject_code', 'academic_year', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};