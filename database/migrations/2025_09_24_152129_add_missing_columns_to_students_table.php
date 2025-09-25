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
        Schema::table('students', function (Blueprint $table) {
            $table->string('ic')->nullable()->after('ic_number');
            $table->string('col_ref_no')->nullable()->after('ic');
            $table->string('student_id')->nullable()->after('col_ref_no');
            $table->string('previous_university')->nullable()->after('student_id');
            $table->string('source_sheet')->nullable()->after('previous_university');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['ic', 'col_ref_no', 'student_id', 'previous_university', 'source_sheet']);
        });
    }
};
