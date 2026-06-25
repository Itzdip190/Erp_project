<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('type')->default('Scholastic');
            $table->string('local_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->integer('sort_order')->default(0);
        });

        // 2. Update sections table
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('assistant_class_teacher_id')->nullable()->constrained('staff')->nullOnDelete();
        });

        // 3. Drop and Recreate section_subject_staff table to support multiple teachers per subject and section-level substitutes
        Schema::dropIfExists('section_subject_staff');

        Schema::create('section_subject_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('substitute_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['section_id', 'subject_id', 'staff_id', 'academic_session_id'],
                'sss_sec_sub_staff_sess_unique'
            );
        });
    }

    public function down(): void
    {
        // For development/refresh purposes
    }
};
