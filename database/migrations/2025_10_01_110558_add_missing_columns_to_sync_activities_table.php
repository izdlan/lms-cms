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
        Schema::table('sync_activities', function (Blueprint $table) {
            // Add missing columns that should exist according to the original migration
            if (!Schema::hasColumn('sync_activities', 'created_count')) {
                $table->integer('created_count')->default(0);
            }
            if (!Schema::hasColumn('sync_activities', 'updated_count')) {
                $table->integer('updated_count')->default(0);
            }
            if (!Schema::hasColumn('sync_activities', 'error_count')) {
                $table->integer('error_count')->default(0);
            }
            if (!Schema::hasColumn('sync_activities', 'processed_sheets')) {
                $table->json('processed_sheets')->nullable();
            }
            if (!Schema::hasColumn('sync_activities', 'source')) {
                $table->string('source')->default('google_drive');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_activities', function (Blueprint $table) {
            // Remove the columns we added
            $table->dropColumn(['created_count', 'updated_count', 'error_count', 'processed_sheets', 'source']);
        });
    }
};
