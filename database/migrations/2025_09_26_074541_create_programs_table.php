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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // e.g., EMBA, MBA, BBA
            $table->string('name'); // e.g., EXECUTIVE MASTER IN BUSINESS ADMINISTRATION
            $table->text('description')->nullable();
            $table->enum('level', ['certificate', 'diploma', 'bachelor', 'master', 'phd'])->default('bachelor');
            $table->integer('duration_months')->nullable(); // Program duration in months
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
