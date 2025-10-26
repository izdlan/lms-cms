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
        Schema::create('master_learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('plo_code', 10); // PLO1, PLO2, etc.
            $table->text('description');
            $table->string('mqf_domain', 50); // C1: Knowledge & Understanding
            $table->string('mqf_code', 10); // C1, C2, etc.
            $table->text('mapped_courses'); // Comma-separated course names
            $table->text('assessment_methods'); // Specific to master level
            $table->text('advanced_knowledge'); // Advanced theoretical knowledge
            $table->text('research_methodology'); // Research methodology skills
            $table->text('leadership_skills'); // Leadership and management skills
            $table->text('critical_thinking'); // Critical analysis skills
            $table->text('dissertation_requirements'); // Dissertation/thesis requirements
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['program_id', 'plo_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_learning_outcomes');
    }
};