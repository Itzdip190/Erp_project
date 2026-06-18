<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('day_of_week'); // e.g. Monday, Tuesday, etc.
            $table->string('start_time');   // e.g. 09:00 AM
            $table->string('end_time');     // e.g. 10:00 AM
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('staff_id'); // teacher
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->string('room_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
