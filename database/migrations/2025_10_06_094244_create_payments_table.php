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
            
            // Invoice system fields
            $table->string('payment_number')->unique()->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Billplz system fields
            $table->string('billplz_id')->unique()->nullable();
            $table->string('billplz_collection_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->default('course_fee'); // course_fee, general_fee, invoice_payment, etc.
            $table->string('reference_id')->nullable(); // course_id, assignment_id, etc.
            $table->string('reference_type')->nullable(); // course, assignment, etc.
            
            // Common fields
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('pending'); // pending, paid, completed, failed, cancelled
            $table->string('payment_method')->nullable(); // fpx, card, ewallet, online_banking, etc.
            $table->string('transaction_id')->nullable();
            $table->text('description')->nullable();
            $table->text('payment_notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->json('billplz_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index(['type', 'reference_id']);
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
