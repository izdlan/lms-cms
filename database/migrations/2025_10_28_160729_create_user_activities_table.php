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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // 'login', 'logout', 'failed_login'
            $table->string('user_role'); // 'student', 'admin', 'lecturer', 'finance_admin'
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('login_method')->nullable(); // 'ic', 'email'
            $table->string('status')->default('success'); // 'success', 'failed', 'blocked'
            $table->text('notes')->nullable(); // Additional info like reason for failure
            $table->timestamps();
            
            $table->index(['user_id', 'activity_type']);
            $table->index(['activity_type', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
