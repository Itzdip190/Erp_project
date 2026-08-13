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
                $table->string('role')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('booking_date')->nullable();
                $table->string('booking_time')->nullable();
                $table->string('timezone')->nullable();
                $table->string('source')->default('Website');
                $table->text('message')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        } else {
            Schema::table('demo_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('demo_bookings', 'booking_date')) {
                    $table->string('booking_date')->nullable()->after('country');
                }
                if (!Schema::hasColumn('demo_bookings', 'booking_time')) {
                    $table->string('booking_time')->nullable()->after('booking_date');
                }
                if (!Schema::hasColumn('demo_bookings', 'timezone')) {
                    $table->string('timezone')->nullable()->after('booking_time');
                }
                if (!Schema::hasColumn('demo_bookings', 'source')) {
                    $table->string('source')->default('Website')->after('timezone');
                }
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
