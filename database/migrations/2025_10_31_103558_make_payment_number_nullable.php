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
        // Make payment_number nullable
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE payments MODIFY COLUMN payment_number VARCHAR(255) NULL');
        
        // Remove unique constraint if it exists and make it nullable
        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropUnique(['payment_number']);
            });
        } catch (\Exception $e) {
            // Index might not exist, ignore
        }
        
        // Re-add unique constraint only for non-null values
        Schema::table('payments', function (Blueprint $table) {
            // Note: MySQL doesn't support partial unique indexes easily
            // For now, we'll keep it nullable without unique constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore as not null (but this might fail if nulls exist)
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE payments MODIFY COLUMN payment_number VARCHAR(255) NOT NULL');
        
        Schema::table('payments', function (Blueprint $table) {
            $table->unique('payment_number');
        });
    }
};
