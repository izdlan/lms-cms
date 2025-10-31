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
        // Check and add columns one by one
        if (!Schema::hasColumn('payments', 'type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('type')->default('course_fee');
            });
        }
        
        if (!Schema::hasColumn('payments', 'reference_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('reference_id')->nullable();
            });
        }
        
        if (!Schema::hasColumn('payments', 'reference_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('reference_type')->nullable();
            });
        }
        
        if (!Schema::hasColumn('payments', 'student_bill_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('student_bill_id')->nullable()->constrained('student_bills')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove columns if they exist
            if (Schema::hasColumn('payments', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
            if (Schema::hasColumn('payments', 'reference_type')) {
                $table->dropColumn('reference_type');
            }
        });
    }
};
