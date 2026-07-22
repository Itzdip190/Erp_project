<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_classes')) {
            Schema::create('exam_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
                $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exam_assessments')) {
            Schema::create('exam_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->string('assessment_type')->default('subject_activity'); // subject_activity, sub_assessment
                $table->string('name');
                $table->text('objective')->nullable();
                $table->string('eval_type')->default('marks'); // marks, grade
                $table->decimal('max_marks', 8, 2)->default(100);
                $table->decimal('weightage_percentage', 5, 2)->default(0);
                $table->integer('display_order')->default(1);
                $table->date('assessment_date')->nullable();
                $table->decimal('pass_marks', 8, 2)->default(33);
                $table->decimal('overall_passing_marks', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exam_sub_assessments')) {
            Schema::create('exam_sub_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->foreignId('exam_assessment_id')->constrained('exam_assessments')->onDelete('cascade');
                $table->string('name');
                $table->decimal('max_marks', 8, 2)->default(100);
                $table->decimal('pass_marks', 8, 2)->default(33);
                $table->decimal('weightage_percentage', 5, 2)->default(0);
                $table->integer('display_order')->default(1);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('exam_subjects')) {
            Schema::table('exam_subjects', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_subjects', 'school_id')) {
                    $table->foreignId('school_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('exam_subjects', 'class_id')) {
                    $table->foreignId('class_id')->nullable()->after('exam_id')->constrained('school_classes')->onDelete('cascade');
                }
                if (!Schema::hasColumn('exam_subjects', 'overall_passing_marks')) {
                    $table->decimal('overall_passing_marks', 5, 2)->nullable()->after('pass_marks');
                }
            });
        }

        if (Schema::hasTable('student_marks')) {
            Schema::table('student_marks', function (Blueprint $table) {
                if (!Schema::hasColumn('student_marks', 'assessment_id')) {
                    $table->foreignId('assessment_id')->nullable()->after('subject_id')->constrained('exam_assessments')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_marks') && Schema::hasColumn('student_marks', 'assessment_id')) {
            Schema::table('student_marks', function (Blueprint $table) {
                $table->dropForeign(['assessment_id']);
                $table->dropColumn('assessment_id');
            });
        }

        if (Schema::hasTable('exam_subjects')) {
            Schema::table('exam_subjects', function (Blueprint $table) {
                if (Schema::hasColumn('exam_subjects', 'overall_passing_marks')) {
                    $table->dropColumn('overall_passing_marks');
                }
                if (Schema::hasColumn('exam_subjects', 'class_id')) {
                    $table->dropForeign(['class_id']);
                    $table->dropColumn('class_id');
                }
                if (Schema::hasColumn('exam_subjects', 'school_id')) {
                    $table->dropForeign(['school_id']);
                    $table->dropColumn('school_id');
                }
            });
        }

        Schema::dropIfExists('exam_sub_assessments');
        Schema::dropIfExists('exam_assessments');
        Schema::dropIfExists('exam_classes');
    }
};
