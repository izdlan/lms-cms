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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['graded_by']);
            
            // Add the correct foreign key constraint to users table
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['graded_by']);
            
            // Restore the original foreign key constraint to lecturers table
            $table->foreign('graded_by')->references('id')->on('lecturers')->onDelete('set null');
        });
    }
};
