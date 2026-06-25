<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. timetable_groups
        Schema::create('timetable_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('group_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('academic_year'); // e.g. "2025-2026"
            $table->time('class_start_time');
            $table->unsignedInteger('number_of_periods');
            $table->json('applicable_days'); // ["Monday","Tuesday",...]
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 2. timetable_group_periods
        Schema::create('timetable_group_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('timetable_group_id')->constrained()->cascadeOnDelete();
            $table->string('period_name'); // "Period 1"
            $table->unsignedInteger('duration_minutes');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. timetable_group_class_section
        Schema::create('timetable_group_class_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('timetable_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['timetable_group_id','class_id','section_id'], 'tg_class_sec_unique');
        });

        // 4. class_subject_teacher
        Schema::create('class_subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['class_id','section_id','subject_id'], 'class_sub_teacher_unique');
        });

        // 5. class_timetable_cells
        Schema::create('class_timetable_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('timetable_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('timetable_group_period_id')->constrained()->cascadeOnDelete();
            $table->string('day_of_week'); // "Monday"
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->enum('mode', ['online','offline'])->default('online');
            $table->timestamps();
            $table->unique(['class_id','section_id','timetable_group_period_id','day_of_week'], 'cell_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_timetable_cells');
        Schema::dropIfExists('class_subject_teacher');
        Schema::dropIfExists('timetable_group_class_section');
        Schema::dropIfExists('timetable_group_periods');
        Schema::dropIfExists('timetable_groups');
    }
};
