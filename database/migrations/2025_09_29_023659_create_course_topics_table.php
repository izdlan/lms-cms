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
        Schema::create('course_topics', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('clo_code'); // Which CLO this topic relates to
            $table->text('topic_title');
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
        Schema::dropIfExists('course_topics');
    }
};
