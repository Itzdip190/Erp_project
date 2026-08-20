<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_payroll_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode', 50)->default('Cash'); // Cash, Bank Transfer, UPI, Cheque, Other
            $table->string('transaction_type', 50)->default('Deposit'); // Salary Advance, Deposit, Adjustment, Other
            $table->text('remark')->nullable();
            $table->decimal('balance_after_transaction', 12, 2)->default(0);
            $table->string('status', 30)->default('completed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_payroll_deposits');
    }
};
