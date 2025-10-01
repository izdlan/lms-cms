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
        Schema::create('sync_activities', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('import'); // import, sync, error, etc.
            $table->string('status'); // success, error, warning
            $table->text('message');
            $table->integer('created_count')->default(0);
            $table->integer('updated_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->json('processed_sheets')->nullable();
            $table->string('source')->default('google_drive'); // google_drive, onedrive, etc.
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Index for efficient querying
            $table->index(['created_at', 'type']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_activities');
    }
};