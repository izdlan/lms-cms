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
        // Keep only EMBA program and remove others
        DB::table('programs')->where('code', '!=', 'EMBA')->delete();
        
        // Update EMBA program details
        DB::table('programs')->where('code', 'EMBA')->update([
            'name' => 'Executive Master in Business Administration',
            'level' => 'master',
            'duration_months' => 12,
            'is_active' => true,
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we're deleting data
        // If needed, you would need to restore from backup
    }
};