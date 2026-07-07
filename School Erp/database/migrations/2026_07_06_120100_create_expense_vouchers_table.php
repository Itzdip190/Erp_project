<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('voucher_no')->nullable();
            $table->unsignedBigInteger('expense_head_id');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('reason');
            $table->text('remarks')->nullable();
            $table->string('document_path')->nullable();
            $table->string('approval_status')->default('Approved'); // Approved, Pending, Rejected
            $table->string('payment_status')->default('Pending');   // Pending, Partial, Paid
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('expense_head_id')->references('id')->on('expense_heads')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_vouchers');
    }
};
