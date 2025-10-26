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
        Schema::create('course_learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('course_name', 100);
            $table->string('clo_code', 10); // CLO1, CLO2, etc.
            $table->text('description');
            $table->string('mqf_domain', 50); // Knowledge & Understanding (C1)
            $table->string('mqf_code', 10); // C1, C2, etc.
            $table->text('topics_covered'); // JSON or text field for topics
            $table->text('assessment_methods'); // JSON or text field for assessment methods
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['program_id', 'course_name', 'clo_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_learning_outcomes');
    }
};
