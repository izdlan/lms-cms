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
        if (Schema::hasTable('invoices')) {
            // Backfill student_bill_id from invoice_id using invoice_number -> bill_number mapping
            if (Schema::hasColumn('payments', 'invoice_id') && Schema::hasColumn('payments', 'student_bill_id')) {
                DB::statement("UPDATE payments p JOIN invoices i ON p.invoice_id = i.id JOIN student_bills b ON b.bill_number = i.invoice_number SET p.student_bill_id = b.id WHERE p.student_bill_id IS NULL");
            }
            if (Schema::hasColumn('receipts', 'invoice_id') && Schema::hasColumn('receipts', 'student_bill_id')) {
                DB::statement("UPDATE receipts r JOIN invoices i ON r.invoice_id = i.id JOIN student_bills b ON b.bill_number = i.invoice_number SET r.student_bill_id = b.id WHERE r.student_bill_id IS NULL");
            }

            // Drop foreign keys and invoice_id columns
            if (Schema::hasColumn('payments', 'invoice_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('invoice_id');
                });
            }
            if (Schema::hasColumn('receipts', 'invoice_id')) {
                Schema::table('receipts', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('invoice_id');
                });
            }

            // Finally drop invoices table
            Schema::drop('invoices');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: we won't recreate invoices; data is consolidated in student_bills
    }
};
