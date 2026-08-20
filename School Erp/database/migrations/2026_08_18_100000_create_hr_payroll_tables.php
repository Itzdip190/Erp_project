<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Staff Attendance Registers (tracks freeze status per month)
        Schema::create('staff_attendance_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('payroll_month', 20); // e.g. '2026-08' or 'Aug 2026'
            $table->boolean('is_frozen')->default(false);
            $table->timestamp('frozen_at')->nullable();
            $table->unsignedBigInteger('frozen_by')->nullable();
            $table->foreign('frozen_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'payroll_month'], 'staff_att_reg_school_month_unique');
        });

        // 2. Staff Payrolls (stores generated & finalised salaries)
        Schema::create('staff_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->string('payroll_month', 20); // e.g. '2026-08' or 'Aug 2026'
            $table->integer('total_days')->default(30);
            $table->decimal('present_days', 8, 2)->default(0);
            $table->decimal('absent_days', 8, 2)->default(0);
            $table->decimal('leave_days', 8, 2)->default(0);
            $table->decimal('half_days', 8, 2)->default(0);
            $table->decimal('payable_days', 8, 2)->default(0);
            $table->decimal('basic_salary', 12, 2)->default(0); // CTC / Monthly Basic
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0); // Calculated Payable Amount
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_balance', 12, 2)->default(0);
            $table->enum('status', ['draft', 'generated', 'finalised'])->default('finalised');
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->boolean('is_frozen')->default(false);
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('finalised_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Strictly prevent duplicate salary generation for the same employee and payroll month
            $table->unique(['school_id', 'staff_id', 'payroll_month'], 'staff_payrolls_school_staff_month_unique');
        });

        // 3. Staff Payroll Payments (stores payment transactions)
        Schema::create('staff_payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_payroll_id')->constrained('staff_payrolls')->cascadeOnDelete();
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->enum('payment_type', ['salary_payment', 'advance_payment'])->default('salary_payment');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50)->default('cash'); // cash, bank_transfer, cheque, upi
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_payroll_payments');
        Schema::dropIfExists('staff_payrolls');
        Schema::dropIfExists('staff_attendance_registers');
    }
};
