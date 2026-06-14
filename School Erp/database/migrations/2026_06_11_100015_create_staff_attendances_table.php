<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'holiday', 'leave']);
            $table->time('clock_in_at')->nullable();
            $table->time('clock_out_at')->nullable();
            $table->enum('attendance_type', ['manual', 'biometric', 'gps']);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->foreign('marked_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            // One attendance record per staff per day
            $table->unique(['school_id', 'staff_id', 'date'], 'staffatt_school_staff_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
