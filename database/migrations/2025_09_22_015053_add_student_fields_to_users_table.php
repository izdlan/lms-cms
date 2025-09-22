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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ic')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('ic');
            $table->enum('role', ['admin', 'student'])->default('student')->after('phone');
            $table->boolean('must_reset_password')->default(false)->after('role');
            $table->json('courses')->nullable()->after('must_reset_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ic', 'phone', 'role', 'must_reset_password', 'courses']);
        });
    }
};
