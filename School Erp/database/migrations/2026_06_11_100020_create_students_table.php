<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('admission_number');           // NO standalone unique — composite below
            $table->unsignedInteger('admission_sequence')->default(0);
            $table->year('admission_year')->nullable();
            $table->string('roll_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('student_categories')->nullOnDelete();
            $table->foreignId('house_id')->nullable()->constrained('student_houses')->nullOnDelete();
            $table->string('photo')->nullable();          // S3 path
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->string('guardian_email')->nullable();
            $table->enum('guardian_relationship', ['father', 'mother', 'guardian']);
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->date('admission_date');
            $table->boolean('is_active')->default(true);
            $table->decimal('opening_due_balance', 10, 2)->default(0);
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Issue 9 Fix — composite unique, NOT standalone unique()
            $table->unique(['school_id', 'admission_number'], 'students_school_admission_unique');

            // Issue 6 Fix — index for guardian_email parent lookups
            $table->index(['school_id', 'guardian_email'], 'students_school_guardian_email_index');

            // Sequence uniqueness per school + year
            $table->unique(
                ['school_id', 'admission_sequence', 'admission_year'],
                'students_school_sequence_year_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
