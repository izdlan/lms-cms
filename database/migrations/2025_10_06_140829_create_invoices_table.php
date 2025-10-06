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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Finance admin who created it
            $table->string('bill_type'); // Tuition Fee, EET Fee, etc.
            $table->string('session'); // Academic session
            $table->decimal('amount', 10, 2);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['invoice_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};