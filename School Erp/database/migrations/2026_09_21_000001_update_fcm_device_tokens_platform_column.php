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
        if (Schema::hasTable('fcm_device_tokens')) {
            try {
                // Change platform column to string to support web, android, and ios without truncation errors
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `fcm_device_tokens` MODIFY COLUMN `platform` VARCHAR(30) NOT NULL DEFAULT 'web'");
            } catch (\Throwable $e) {
                // Fallback for sqlite / non-mysql
                Schema::table('fcm_device_tokens', function (Blueprint $table) {
                    $table->string('platform', 30)->default('web')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('fcm_device_tokens')) {
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `fcm_device_tokens` MODIFY COLUMN `platform` ENUM('android', 'ios') NOT NULL");
            } catch (\Throwable $e) {
                // Silent fallback
            }
        }
    }
};
