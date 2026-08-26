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
        // 1. Daily Task Heads (Categories / Rubrics)
        if (!Schema::hasTable('daily_task_heads')) {
            Schema::create('daily_task_heads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->string('name', 150);
                $table->string('code', 50)->nullable();
                $table->text('description')->nullable();
                $table->string('color', 30)->default('#4f46e5');
                $table->string('icon', 60)->default('fa-tasks');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            });
        }

        // 2. Daily Task Questions (Class & Subject Wise Tasks)
        if (!Schema::hasTable('daily_task_questions')) {
            Schema::create('daily_task_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('daily_task_head_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->nullable()->index(); // Nullable = All Classes
                $table->unsignedBigInteger('section_id')->nullable()->index(); // Nullable = All Sections
                $table->unsignedBigInteger('subject_id')->nullable()->index(); // Nullable = For Class Teacher, or Specific Subject
                $table->string('target_role', 30)->default('both'); // 'both', 'class_teacher', 'subject_teacher'
                $table->text('question');
                $table->string('evaluation_type', 30)->default('rating'); // 'rating' (1-5 stars), 'score' (marks), 'options' (Good/Avg/etc), 'boolean' (Yes/No), 'remark'
                $table->integer('max_score')->default(5);
                $table->boolean('is_mandatory')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('daily_task_head_id')->references('id')->on('daily_task_heads')->onDelete('set null');
                $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('cascade');
                $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            });
        }

        // 3. Daily Task Reviews (Session/Daily Entry Master)
        if (!Schema::hasTable('daily_task_reviews')) {
            Schema::create('daily_task_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('academic_session_id')->nullable()->index();
                $table->date('date')->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index(); // Staff ID
                $table->string('review_type', 30)->default('class_teacher'); // 'class_teacher' or 'subject_teacher'
                $table->text('overall_remarks')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('cascade');
                $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
                $table->foreign('teacher_id')->references('id')->on('staff')->onDelete('set null');
            });
        }

        // 4. Daily Task Evaluations (Student-level ratings & remarks per question)
        if (!Schema::hasTable('daily_task_evaluations')) {
            Schema::create('daily_task_evaluations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('daily_task_review_id')->index();
                $table->unsignedBigInteger('daily_task_question_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->date('date')->index();
                $table->integer('rating')->nullable(); // 1 to 5
                $table->decimal('score', 6, 2)->nullable();
                $table->string('status_option', 50)->nullable(); // e.g., 'Excellent', 'Good', 'Needs Improvement', 'Done', 'Not Done'
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('daily_task_review_id')->references('id')->on('daily_task_reviews')->onDelete('cascade');
                $table->foreign('daily_task_question_id')->references('id')->on('daily_task_questions')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_task_evaluations');
        Schema::dropIfExists('daily_task_reviews');
        Schema::dropIfExists('daily_task_questions');
        Schema::dropIfExists('daily_task_heads');
    }
};
