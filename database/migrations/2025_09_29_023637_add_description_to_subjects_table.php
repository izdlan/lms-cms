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
        Schema::table('subjects', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('subjects', 'assessment_methods')) {
                $table->string('assessment_methods')->nullable()->after('description');
            }
            if (!Schema::hasColumn('subjects', 'duration')) {
                $table->string('duration')->nullable()->after('assessment_methods');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            //
        });
    }
};
