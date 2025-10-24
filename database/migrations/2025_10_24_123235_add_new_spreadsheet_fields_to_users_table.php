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
            // Add new fields from the updated spreadsheet structure
            $table->string('status')->nullable()->after('name'); // STATUS
            $table->string('ic_passport')->nullable()->after('ic'); // IC / PASSPORT (rename from ic)
            $table->string('contact_no')->nullable()->after('phone'); // CONTACT NO.
            $table->string('student_portal')->nullable()->after('student_portal_password'); // STUDENT PORTAL
            $table->decimal('total_fees', 10, 2)->nullable()->after('col_date'); // TOTAL FEES
            $table->string('transaction_month')->nullable()->after('total_fees'); // TRANSACTION MONTH
            $table->text('remarks')->nullable()->after('transaction_month'); // REMARKS
            $table->string('pic')->nullable()->after('remarks'); // PIC
            
            // Remove old fields that are no longer needed
            $table->dropColumn('previous_university'); // No longer in new spreadsheet
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the new fields
            $table->dropColumn([
                'status',
                'ic_passport',
                'contact_no',
                'student_portal',
                'total_fees',
                'transaction_month',
                'remarks',
                'pic'
            ]);
            
            // Restore the old field
            $table->string('previous_university')->nullable();
        });
    }
};
