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
        Schema::create('ex_students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique(); // e.g., 670219-08-6113
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('program')->nullable();
            $table->string('graduation_year');
            $table->string('graduation_month')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->string('certificate_number')->unique();
            $table->string('qr_code')->unique(); // QR code data
            $table->json('academic_records')->nullable(); // Transcript data
            $table->json('certificate_data')->nullable(); // Certificate details
            $table->boolean('is_verified')->default(false);
            $table->timestamp('last_accessed')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'is_verified']);
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ex_students');
    }
};
