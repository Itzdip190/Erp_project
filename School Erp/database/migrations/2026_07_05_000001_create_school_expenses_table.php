<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('title');
            $table->string('category'); // salary, maintenance, utilities, transport, supplies, other
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_mode')->default('cash'); // cash, bank_transfer, cheque, upi
            $table->text('description')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('paid_to')->nullable();    // vendor / payee name
            $table->string('status')->default('paid'); // paid, pending, cancelled
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id', 'expense_date']);
            $table->index(['school_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_expenses');
    }
};
