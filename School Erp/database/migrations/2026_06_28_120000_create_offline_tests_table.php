<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('offline_tests')) {
            Schema::create('offline_tests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->string('academic_year')->default('Apr 2025 - Mar 2026');
                $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('cascade');
                $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
                $table->foreignId('teacher_id')->nullable()->constrained('staff')->onDelete('set null');
                $table->string('title');
                $table->string('chapters')->nullable();
                $table->string('sub_chapters')->nullable();
                $table->text('instructions')->nullable();
                $table->dateTime('start_date_time')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->string('grading_type')->default('Marks');
                $table->string('status')->default('published');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_tests');
    }
};
