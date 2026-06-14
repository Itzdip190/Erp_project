<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('school_id');
            $table->text('token');
            $table->string('device_name');
            $table->enum('platform', ['android', 'ios']);
            $table->timestamps();

            // Each device replaces its own token — no duplicates per user+device
            $table->unique(['user_id', 'device_name'], 'fcm_user_device_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_device_tokens');
    }
};
