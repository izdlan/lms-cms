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
        if (!Schema::hasTable('student_bills')) {
        Schema::create('student_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session');
            $table->string('bill_type'); // Tuition Fee, EET Fee, Library Fee, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('pending'); // pending, paid, overdue, cancelled
            $table->date('bill_date');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional bill details
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['bill_type', 'status']);
            $table->index('due_date');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_bills');
    }
};
