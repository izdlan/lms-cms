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
        Schema::table('ex_students', function (Blueprint $table) {
            // Add program_short for short program name (e.g., "Bachelor of Science")
            $table->string('program_short')->nullable()->after('program');
            // Add program_full for full program name (e.g., "Bachelor of Science (Hons) in Information & Communication Technology")
            $table->string('program_full')->nullable()->after('program_short');
            // Add graduation_day for day of month (1-31)
            $table->integer('graduation_day')->nullable()->after('graduation_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ex_students', function (Blueprint $table) {
            $table->dropColumn(['program_short', 'program_full', 'graduation_day']);
        });
    }
};
