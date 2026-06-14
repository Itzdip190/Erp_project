<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_id');
            // Issue 3 Fix — composite unique: same employee_id can exist in different schools
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained()->cascadeOnDelete();
            $table->enum('employment_type', ['permanent', 'contract', 'part_time'])->default('permanent');
            $table->string('qualification')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('photo')->nullable();             // S3 path
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('pan_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Issue 3 Fix — composite unique (not standalone)
            $table->unique(['school_id', 'employee_id'], 'staff_school_employee_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
