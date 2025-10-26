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
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('subject_name', 200);
            $table->string('subject_code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('classification', 50); // Common Core, Elective, etc.
            $table->integer('credit_hours');
            $table->text('teaching_hours')->nullable(); // 16-Hour Intensive + 8-Hours Online Support
            $table->text('assessment_methods')->nullable(); // JSON or text field
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['program_id', 'subject_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_subjects');
    }
};
