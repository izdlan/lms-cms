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
        Schema::create('course_clos', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('clo_code'); // CLO1, CLO2, etc.
            $table->text('description');
            $table->string('mqf_alignment');
            $table->integer('order')->default(1);
            $table->timestamps();
            
            $table->foreign('subject_code')->references('code')->on('subjects')->onDelete('cascade');
            $table->index(['subject_code', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_clos');
    }
};
