<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->text('classes'); // Comma-separated or JSON of classes
            $table->integer('no_of_installments');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('fee_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('head_name');
            $table->string('component_name');
            $table->string('admission_type')->default('All Students'); // All, New, Existing
            $table->string('gender')->default('All Students'); // All, Male, Female
            $table->timestamps();
        });

        Schema::create('fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('name');
            $table->text('remarks')->nullable();
            $table->text('classes_installments')->nullable(); // JSON or text mapping
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->text('student_ids')->nullable(); // JSON of targeted students
            $table->timestamps();
        });

        Schema::create('misc_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('name');
            $table->text('remarks')->nullable();
            $table->text('classes_installments')->nullable(); // JSON or text mapping
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->text('student_ids')->nullable(); // JSON of targeted students
            $table->timestamps();
        });

        Schema::create('fee_fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('name');
            $table->string('fine_type')->default('Fixed Amount'); // Fixed Amount, Daily
            $table->decimal('fine_amount', 10, 2)->default(0.00);
            $table->boolean('status')->default(true); // Active/Inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_fines');
        Schema::dropIfExists('misc_fees');
        Schema::dropIfExists('fee_discounts');
        Schema::dropIfExists('fee_components');
        Schema::dropIfExists('fee_schedules');
    }
};
