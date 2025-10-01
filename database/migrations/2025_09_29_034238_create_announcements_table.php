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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('class_code');
            $table->string('title');
            $table->text('content');
            $table->string('author_name');
            $table->string('author_email');
            $table->boolean('is_important')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->foreign('subject_code')->references('code')->on('subjects');
            $table->index(['subject_code', 'class_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
