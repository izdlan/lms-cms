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
            $table->boolean('accounting_synced')->default(false)->after('paid_at');
            $table->timestamp('accounting_synced_at')->nullable()->after('accounting_synced');
            $table->text('accounting_sync_error')->nullable()->after('accounting_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['accounting_synced', 'accounting_synced_at', 'accounting_sync_error']);
        });
    }
};
