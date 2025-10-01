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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->json('attachments')->nullable(); // Store file paths
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
            $table->boolean('is_late')->default(false);
            $table->datetime('submitted_at');
            $table->datetime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('lecturers')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['assignment_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['assignment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
