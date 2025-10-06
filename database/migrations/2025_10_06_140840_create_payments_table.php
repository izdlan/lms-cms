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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['online_banking', 'credit_card', 'debit_card', 'bank_transfer', 'cash', 'other']);
            $table->string('transaction_id')->nullable(); // External payment gateway transaction ID
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_notes')->nullable();
            $table->json('payment_details')->nullable(); // Store additional payment gateway details
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};