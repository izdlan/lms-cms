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
            $table->string('certificate_number')->nullable()->unique();
            $table->date('graduation_date')->nullable();
            $table->boolean('certificate_generated')->default(false);
            $table->timestamp('certificate_generated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_number',
                'graduation_date', 
                'certificate_generated',
                'certificate_generated_at'
            ]);
        });
    }
};
