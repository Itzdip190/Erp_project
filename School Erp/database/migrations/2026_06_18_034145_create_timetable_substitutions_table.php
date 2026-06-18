<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_substitutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('timetable_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('original_staff_id');
            $table->foreign('original_staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->unsignedBigInteger('substitute_staff_id');
            $table->foreign('substitute_staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->string('status')->default('active'); // active, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_substitutions');
    }
};
