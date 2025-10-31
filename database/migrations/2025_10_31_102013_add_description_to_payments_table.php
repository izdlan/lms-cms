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
        // Add description if it doesn't exist
        if (!Schema::hasColumn('payments', 'description')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
        
        // Add payment_method if it doesn't exist
        if (!Schema::hasColumn('payments', 'payment_method')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_method')->nullable();
            });
        }
        
        // Add amount if it doesn't exist
        if (!Schema::hasColumn('payments', 'amount')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->decimal('amount', 10, 2);
            });
        }
        
        // Add transaction_id if it doesn't exist
        if (!Schema::hasColumn('payments', 'transaction_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('transaction_id')->nullable();
            });
        }
        
        // Add paid_at if it doesn't exist
        if (!Schema::hasColumn('payments', 'paid_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->timestamp('paid_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
