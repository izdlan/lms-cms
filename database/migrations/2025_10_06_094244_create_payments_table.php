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
            $table->string('billplz_id')->unique();
            $table->string('billplz_collection_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('course_fee'); // course_fee, general_fee, etc.
            $table->string('reference_id')->nullable(); // course_id, assignment_id, etc.
            $table->string('reference_type')->nullable(); // course, assignment, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('pending'); // pending, paid, failed, cancelled
            $table->string('payment_method')->nullable(); // fpx, card, ewallet
            $table->string('transaction_id')->nullable();
            $table->text('description');
            $table->json('billplz_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['type', 'reference_id']);
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
