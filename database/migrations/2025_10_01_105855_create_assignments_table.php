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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('subject_code');
            $table->string('class_code');
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->decimal('total_marks', 5, 2);
            $table->decimal('passing_marks', 5, 2);
            $table->datetime('due_date');
            $table->datetime('available_from');
            $table->enum('type', ['individual', 'group'])->default('individual');
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->json('attachments')->nullable(); // Store file paths
            $table->text('instructions')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_penalty_percentage')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('subject_code')->references('code')->on('subjects')->onDelete('cascade');
            $table->foreign('class_code')->references('class_code')->on('class_schedules')->onDelete('cascade');
            $table->index(['subject_code', 'class_code']);
            $table->index(['lecturer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
