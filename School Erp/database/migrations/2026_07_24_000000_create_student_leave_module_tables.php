<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cleanup old/incomplete student leave entries if they exist
        if (Schema::hasTable('leave_applications')) {
            // Delete old student leave records to allow a fresh student leave workflow restart
            DB::table('leave_applications')
                ->where('applicant_type', 'student')
                ->orWhereNotNull('student_id')
                ->delete();
        }

        // 2. Settings table for Student Leave
        if (!Schema::hasTable('student_leave_settings')) {
            Schema::create('student_leave_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->boolean('use_acknowledgement')->default(false);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->unique('school_id');
            });
        }

        // 3. Declarations table for Student Leave
        if (!Schema::hasTable('student_leave_declarations')) {
            Schema::create('student_leave_declarations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('title');
                $table->text('declaration_text');
                $table->boolean('is_enabled')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->index(['school_id', 'is_enabled']);
            });
        }

        // 4. Main Student Leave Applications table
        if (!Schema::hasTable('student_leave_applications')) {
            Schema::create('student_leave_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('academic_year')->nullable();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('section_id')->nullable();
                
                $table->enum('leave_type', ['Leave', 'Sick Leave'])->default('Leave');
                $table->string('title');
                $table->text('reason')->nullable();
                $table->date('from_date');
                $table->date('to_date');
                $table->integer('total_days')->default(1);
                $table->string('attachment_path')->nullable();

                $table->unsignedBigInteger('declaration_id')->nullable();
                $table->boolean('declaration_accepted')->default(false);

                // Status: pending, approved, rejected, acknowledged
                $table->string('status')->default('pending');
                $table->text('admin_remarks')->nullable();
                $table->unsignedBigInteger('action_by')->nullable();
                $table->timestamp('action_at')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->foreign('declaration_id')->references('id')->on('student_leave_declarations')->onDelete('set null');
                $table->index(['school_id', 'student_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_leave_applications');
        Schema::dropIfExists('student_leave_declarations');
        Schema::dropIfExists('student_leave_settings');
    }
};
