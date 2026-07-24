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
        if (!Schema::hasTable('report_card_histories')) {
            Schema::create('report_card_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('academic_session_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();
                $table->string('exam_name')->default('All Exams');
                $table->string('title')->default('Annual Progress Report Card');
                $table->string('layout_style')->nullable()->default('cbse_standard');
                $table->unsignedBigInteger('template_id')->nullable();
                $table->string('template_name')->nullable()->default('CBSE Classic Standard');
                $table->integer('student_count')->default(0);
                $table->boolean('is_sent_to_students')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('report_card_history_students')) {
            Schema::create('report_card_history_students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('report_card_history_id')->index();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('section_id')->nullable();
                $table->string('exam_name')->nullable();
                $table->unsignedBigInteger('template_id')->nullable();
                $table->string('total_marks')->nullable();
                $table->float('percentage', 5, 2)->nullable();
                $table->string('grade', 10)->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->foreign('report_card_history_id')->references('id')->on('report_card_histories')->onDelete('cascade');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_card_history_students');
        Schema::dropIfExists('report_card_histories');
    }
};
