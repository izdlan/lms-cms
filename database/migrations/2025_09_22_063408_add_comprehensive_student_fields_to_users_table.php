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
            // Academic Information (col_ref_no and student_id already exist)
            $table->string('category')->nullable()->after('source_sheet');
            $table->string('programme_name')->nullable()->after('category');
            $table->string('faculty')->nullable()->after('programme_name');
            $table->string('programme_code')->nullable()->after('faculty');
            $table->string('semester_entry')->nullable()->after('programme_code');
            $table->string('programme_intake')->nullable()->after('semester_entry');
            $table->date('date_of_commencement')->nullable()->after('programme_intake');
            
            // Research Information
            $table->text('research_title')->nullable()->after('date_of_commencement');
            $table->string('supervisor')->nullable()->after('research_title');
            $table->string('external_examiner')->nullable()->after('supervisor');
            $table->string('internal_examiner')->nullable()->after('external_examiner');
            
            // Student Portal Information
            $table->string('student_portal_username')->nullable()->after('internal_examiner');
            $table->string('student_portal_password')->nullable()->after('student_portal_username');
            
            // Additional Dates
            $table->date('col_date')->nullable()->after('student_portal_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'programme_name',
                'faculty',
                'programme_code',
                'semester_entry',
                'programme_intake',
                'date_of_commencement',
                'research_title',
                'supervisor',
                'external_examiner',
                'internal_examiner',
                'student_portal_username',
                'student_portal_password',
                'col_date'
            ]);
        });
    }
};
