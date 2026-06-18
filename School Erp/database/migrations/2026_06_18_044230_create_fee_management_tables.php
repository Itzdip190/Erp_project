<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('fee_category_id')->constrained('fee_categories')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('schedule_type')->default('monthly'); // monthly, quarterly, annually
            $table->timestamps();
        });

        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_category_id')->constrained('fee_categories')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, paid, partially_paid
            $table->timestamps();
        });

        Schema::create('fee_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_mode')->default('cash'); // cash, online, cheque
            $table->string('transaction_id')->nullable();
            $table->date('payment_date');
            $table->timestamps();
        });

        Schema::create('pending_cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');
            $table->string('cheque_number');
            $table->decimal('amount', 10, 2);
            $table->date('cheque_date');
            $table->string('status')->default('pending'); // pending, cleared, bounced
            $table->timestamps();
        });

        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('purpose');
            $table->string('link_url');
            $table->string('status')->default('active'); // active, paid, expired
            $table->timestamps();
        });

        Schema::create('fee_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('refund_date');
            $table->string('reason');
            $table->timestamps();
        });

        Schema::create('optional_fee_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_category_id')->constrained('fee_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('optional_fee_mappings');
        Schema::dropIfExists('fee_refunds');
        Schema::dropIfExists('payment_links');
        Schema::dropIfExists('pending_cheques');
        Schema::dropIfExists('fee_receipts');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_categories');
    }
};
