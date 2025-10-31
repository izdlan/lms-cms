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
        // Add billplz_id if it doesn't exist
        if (!Schema::hasColumn('payments', 'billplz_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('billplz_id')->unique()->nullable();
            });
        }
        
        // Add billplz_collection_id if it doesn't exist
        if (!Schema::hasColumn('payments', 'billplz_collection_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('billplz_collection_id')->nullable();
            });
        }
        
        // Add status if it doesn't exist
        if (!Schema::hasColumn('payments', 'status')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('status')->default('pending');
            });
        }
        
        // Add expires_at if it doesn't exist
        if (!Schema::hasColumn('payments', 'expires_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable();
            });
        }
        
        // Add billplz_response if it doesn't exist
        if (!Schema::hasColumn('payments', 'billplz_response')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->json('billplz_response')->nullable();
            });
        }
        
        // Add currency if it doesn't exist
        if (!Schema::hasColumn('payments', 'currency')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('currency', 3)->default('MYR');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'billplz_id')) {
                $table->dropColumn('billplz_id');
            }
            if (Schema::hasColumn('payments', 'billplz_collection_id')) {
                $table->dropColumn('billplz_collection_id');
            }
            if (Schema::hasColumn('payments', 'billplz_response')) {
                $table->dropColumn('billplz_response');
            }
        });
    }
};
