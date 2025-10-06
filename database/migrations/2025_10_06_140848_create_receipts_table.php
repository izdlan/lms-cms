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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['online_banking', 'credit_card', 'debit_card', 'bank_transfer', 'cash', 'other']);
            $table->timestamp('payment_date');
            $table->text('receipt_notes')->nullable();
            $table->string('pdf_path')->nullable(); // Path to generated PDF receipt
            $table->timestamps();
            
            $table->index(['student_id', 'payment_date']);
            $table->index(['payment_id', 'invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};