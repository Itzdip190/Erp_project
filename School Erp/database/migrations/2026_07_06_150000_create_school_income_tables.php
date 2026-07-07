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
        // 1. Income Heads
        Schema::create('income_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Income Vouchers
        Schema::create('income_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('voucher_no')->nullable();
            $table->unsignedBigInteger('income_head_id');
            $table->decimal('amount', 12, 2);
            $table->date('income_date');
            $table->string('reason');
            $table->text('remarks')->nullable();
            $table->string('document_path')->nullable();
            $table->string('approval_status')->default('Approved'); // Approved, Pending, Rejected
            $table->string('payment_status')->default('Pending');   // Pending, Partial, Paid
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('income_head_id')->references('id')->on('income_heads')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Voucher Receipts (mirrors voucher_payments)
        Schema::create('voucher_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('income_voucher_id');
            $table->date('receipt_date');
            $table->string('invoice_no')->nullable();
            $table->string('payment_mode')->default('cash'); // cash, bank_transfer, cheque, upi
            $table->string('bank_name')->nullable();
            $table->date('check_issue_date')->nullable();
            $table->string('branch')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('income_voucher_id')->references('id')->on('income_vouchers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 4. School Incomes (mirrors school_expenses)
        Schema::create('school_incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('income_head_id')->nullable();
            $table->unsignedBigInteger('income_voucher_id')->nullable();
            $table->unsignedBigInteger('voucher_receipt_id')->nullable();
            $table->string('title');
            $table->string('category')->default('other'); // fees, admissions, transport, sales, donations, events, other
            $table->decimal('amount', 12, 2);
            $table->date('income_date');
            $table->string('payment_mode')->default('cash'); // cash, bank_transfer, cheque, upi
            $table->string('bank_name')->nullable();
            $table->date('check_issue_date')->nullable();
            $table->string('branch')->nullable();
            $table->text('description')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('received_from')->nullable(); // payee/payer name
            $table->string('status')->default('paid'); // paid, pending, cancelled
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('income_head_id')->references('id')->on('income_heads')->onDelete('set null');
            $table->foreign('income_voucher_id')->references('id')->on('income_vouchers')->onDelete('set null');
            $table->foreign('voucher_receipt_id')->references('id')->on('voucher_receipts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['school_id', 'income_date']);
            $table->index(['school_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_incomes');
        Schema::dropIfExists('voucher_receipts');
        Schema::dropIfExists('income_vouchers');
        Schema::dropIfExists('income_heads');
    }
};
