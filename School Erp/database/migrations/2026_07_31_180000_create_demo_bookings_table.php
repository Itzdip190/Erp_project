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
        if (!Schema::hasTable('demo_bookings')) {
            Schema::create('demo_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('email');
                $table->string('phone');
                $table->string('institute_name')->nullable();
                $table->string('student_count')->nullable();
                $table->string('role');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->text('message')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_bookings');
    }
};
