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
        // Check if user_id column exists in lecturers table, if not add it
        if (!Schema::hasColumn('lecturers', 'user_id')) {
            Schema::table('lecturers', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            });
        }

        // Check if students table exists and add user_id if needed
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'user_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            });
        }

        // Update users table to ensure it has all necessary columns
        Schema::table('users', function (Blueprint $table) {
            // Ensure these columns exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['student', 'lecturer', 'admin'])->default('student')->after('email');
            }
            if (!Schema::hasColumn('users', 'must_reset_password')) {
                $table->boolean('must_reset_password')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('must_reset_password');
            }
        });

        // Migrate existing data
        $this->migrateExistingData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraints and columns
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }

    /**
     * Migrate existing data to new structure
     */
    private function migrateExistingData()
    {
        // Migrate lecturers data
        $lecturers = DB::table('lecturers')->get();
        
        foreach ($lecturers as $lecturer) {
            // Check if user already exists
            $existingUser = DB::table('users')->where('email', $lecturer->email)->first();
            
            if ($existingUser) {
                // Update existing user
                DB::table('users')
                    ->where('id', $existingUser->id)
                    ->update([
                        'role' => 'lecturer',
                        'name' => $lecturer->name,
                        'updated_at' => now()
                    ]);
                
                // Link lecturer to user
                DB::table('lecturers')
                    ->where('id', $lecturer->id)
                    ->update(['user_id' => $existingUser->id]);
            } else {
                // Create new user for lecturer
                $userId = DB::table('users')->insertGetId([
                    'name' => $lecturer->name,
                    'email' => $lecturer->email,
                    'password' => bcrypt('000000'), // Default password
                    'role' => 'lecturer',
                    'must_reset_password' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Link lecturer to user
                DB::table('lecturers')
                    ->where('id', $lecturer->id)
                    ->update(['user_id' => $userId]);
            }
        }

        // Migrate students data (if students table exists)
        if (Schema::hasTable('students')) {
            $students = DB::table('students')->get();
            
            foreach ($students as $student) {
                // Check if user already exists
                $existingUser = DB::table('users')->where('email', $student->email)->first();
                
                if ($existingUser) {
                    // Update existing user
                    DB::table('users')
                        ->where('id', $existingUser->id)
                        ->update([
                            'role' => 'student',
                            'name' => $student->name,
                            'updated_at' => now()
                        ]);
                    
                    // Link student to user
                    DB::table('students')
                        ->where('id', $student->id)
                        ->update(['user_id' => $existingUser->id]);
                } else {
                    // Create new user for student
                    $userId = DB::table('users')->insertGetId([
                        'name' => $student->name,
                        'email' => $student->email,
                        'password' => bcrypt('000000'), // Default password
                        'role' => 'student',
                        'must_reset_password' => true,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Link student to user
                    DB::table('students')
                        ->where('id', $student->id)
                        ->update(['user_id' => $userId]);
                }
            }
        }

        // Update all existing users to have default password 000000
        DB::table('users')->update([
            'password' => bcrypt('000000'),
            'must_reset_password' => true,
            'updated_at' => now()
        ]);
    }
};