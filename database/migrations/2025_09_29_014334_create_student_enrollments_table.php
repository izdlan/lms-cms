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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('program_code');
            $table->string('subject_code');
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->onDelete('set null');
            $table->string('class_code')->nullable();
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->decimal('grade', 3, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'subject_code']);
            $table->index(['program_code', 'subject_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
