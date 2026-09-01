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
        if (!Schema::hasTable('school_restore_logs')) {
            Schema::create('school_restore_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index(); // Super Admin who performed restore
                $table->unsignedBigInteger('school_id')->index(); // Target school tenant
                $table->string('school_code', 50)->nullable();
                $table->string('snapshot_file', 255);
                $table->string('snapshot_timestamp', 50)->nullable();
                $table->json('modules_selected'); // Array of module keys
                $table->json('tables_restored')->nullable(); // Table => rows inserted/deleted map
                $table->unsignedInteger('total_rows_deleted')->default(0);
                $table->unsignedInteger('total_rows_inserted')->default(0);
                $table->unsignedInteger('total_files_restored')->default(0);
                $table->string('status', 30)->default('success'); // 'success', 'failed', 'rolled_back'
                $table->text('error_message')->nullable();
                $table->unsignedInteger('duration_ms')->default(0);
                $table->string('pre_restore_backup', 255)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_restore_logs');
    }
};
