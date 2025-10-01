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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('class_code')->unique();
            $table->string('subject_code');
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->string('program_code');
            $table->string('class_name');
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_students')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['subject_code', 'program_code']);
            $table->index(['lecturer_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
