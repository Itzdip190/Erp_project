<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'holiday', 'leave', 'duty_leave']);
            $table->unsignedBigInteger('marked_by');
            $table->foreign('marked_by')->references('id')->on('users')->cascadeOnDelete();
            $table->string('remark')->nullable();
            $table->enum('attendance_type', ['manual', 'biometric', 'qr', 'face']);
            $table->timestamps();

            // One record per student per day
            $table->unique(['school_id', 'student_id', 'date'], 'sa_school_student_date_unique');

            // Fast lookups: section attendance for a date
            $table->index(['school_id', 'section_id', 'date'], 'sa_school_section_date_index');

            // Fast lookups: student attendance history
            $table->index(['school_id', 'student_id', 'date'], 'sa_school_student_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
