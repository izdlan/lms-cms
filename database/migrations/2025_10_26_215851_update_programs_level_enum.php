<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the ENUM to include 'degree'
        DB::statement("ALTER TABLE programs MODIFY COLUMN level ENUM('certificate', 'diploma', 'degree', 'bachelor', 'master', 'phd') DEFAULT 'bachelor'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM
        DB::statement("ALTER TABLE programs MODIFY COLUMN level ENUM('certificate', 'diploma', 'bachelor', 'master', 'phd') DEFAULT 'bachelor'");
    }
};