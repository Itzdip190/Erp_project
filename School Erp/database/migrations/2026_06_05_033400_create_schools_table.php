<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();                      // e.g. "YIS2024" — all mobile logins
            $table->string('custom_domain')->unique()->nullable();
            $table->string('logo')->nullable();                    // S3 path
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('dashboard_theme')->default('blue');
            $table->string('status')->default('trial');            // active / suspended / trial
            $table->json('sms_config')->nullable();               // OTP SMS credentials
            $table->integer('late_grace_minutes')->default(15);
            $table->time('staff_punch_in_start')->default('08:00:00');
            $table->time('staff_punch_in_end')->default('18:00:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
