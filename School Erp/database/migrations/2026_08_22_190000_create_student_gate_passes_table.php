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
        if (!Schema::hasTable('student_gate_passes')) {
            Schema::create('student_gate_passes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('academic_session_id')->nullable()->constrained()->nullOnDelete();
                $table->string('gate_pass_number'); // e.g. 2026/GP/1 or GP/2026/000001
                $table->dateTime('pass_date');
                $table->string('template')->default('classic');
                $table->string('reason');
                $table->string('guardian_type')->default('father'); // father, mother, guardian, other
                $table->string('guardian_name');
                $table->string('guardian_relation');
                $table->string('guardian_phone')->nullable();
                $table->string('expected_return_time')->nullable();
                $table->dateTime('actual_return_time')->nullable();
                $table->enum('status', ['draft', 'generated', 'issued', 'returned', 'cancelled'])->default('issued');
                $table->text('remarks')->nullable();
                $table->string('approved_by')->nullable();
                $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();

                // School profile snapshot overrides
                $table->string('school_name')->nullable();
                $table->string('school_logo')->nullable();
                $table->string('school_address')->nullable();
                $table->string('school_city')->nullable();
                $table->string('school_state')->nullable();
                $table->string('school_pincode')->nullable();
                $table->string('school_phone')->nullable();
                $table->string('school_email')->nullable();

                // Student & Extra snapshots
                $table->json('student_snapshot')->nullable();
                $table->json('meta_data')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['school_id', 'gate_pass_number']);
                $table->index(['school_id', 'student_id']);
                $table->index(['school_id', 'status']);
                $table->index(['school_id', 'pass_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_gate_passes');
    }
};
