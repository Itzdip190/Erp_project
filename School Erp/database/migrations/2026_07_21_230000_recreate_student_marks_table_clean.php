<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('student_marks');

        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
            $table->foreignId('sub_assessment_id')->nullable()->constrained('exam_assessments')->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained('exam_assessments')->onDelete('cascade');
            $table->string('assessment_name')->nullable();
            $table->string('exam_name');
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->decimal('max_marks', 5, 2)->default(100.00);
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->string('attendance_status')->default('present');
            $table->text('achievements')->nullable();
            $table->boolean('is_retest')->default(false);
            $table->decimal('retest_marks', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['school_id', 'student_id', 'subject_id', 'exam_name'], 'idx_student_marks_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};
