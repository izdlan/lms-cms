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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'student_bill_id')) {
                $table->foreignId('student_bill_id')->nullable()->after('invoice_id')->constrained('student_bills')->nullOnDelete();
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('receipts', 'student_bill_id')) {
                $table->foreignId('student_bill_id')->nullable()->after('invoice_id')->constrained('student_bills')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'student_bill_id')) {
                $table->dropConstrainedForeignId('student_bill_id');
            }
        });

        Schema::table('receipts', function (Blueprint $table) {
            if (Schema::hasColumn('receipts', 'student_bill_id')) {
                $table->dropConstrainedForeignId('student_bill_id');
            }
        });
    }
};
