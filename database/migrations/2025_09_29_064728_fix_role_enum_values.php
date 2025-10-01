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
        // First, temporarily change 'staff' to 'admin' to avoid enum conflicts
        DB::table('users')->where('role', 'staff')->update(['role' => 'admin']);
        
        // Now update the enum to include 'lecturer'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'lecturer', 'admin') DEFAULT 'student'");
        
        // Finally, change the temporary 'admin' back to 'lecturer' for staff users
        // We need to identify which users should be lecturers vs admins
        // For now, let's assume all current 'admin' users should be 'lecturer'
        // (You can adjust this logic based on your needs)
        DB::table('users')->where('role', 'admin')->update(['role' => 'lecturer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change 'lecturer' back to 'staff'
        DB::table('users')->where('role', 'lecturer')->update(['role' => 'staff']);
        
        // Revert enum to original values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'staff', 'admin') DEFAULT 'student'");
    }
};