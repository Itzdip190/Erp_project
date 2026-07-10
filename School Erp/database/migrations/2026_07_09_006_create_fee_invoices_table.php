<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('related_invoice_id')->nullable();
            $table->string('related_invoice_number')->nullable();
            $table->integer('installment_no');
            $table->string('type'); // payment, cancel_payment, repayment, refund, cancel_refund
            $table->string('status'); // paid, cancelled, refunded, pending
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('payment_mode')->nullable();
            $table->date('payment_date');
            $table->longText('payment_details')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('related_invoice_id')->references('id')->on('fee_invoices')->onDelete('set null');

            // Indexes
            $table->index('invoice_number');
            $table->index('related_invoice_number');
        });

        // Backfill legacy receipts
        $receipts = DB::table('fee_receipts')->get();
        foreach ($receipts as $receipt) {
            $instNo = 1;
            $details = null;
            if (!empty($receipt->payment_details)) {
                $decoded = json_decode($receipt->payment_details, true);
                if (is_array($decoded) && isset($decoded[0]['student_fee_id'])) {
                    $details = $receipt->payment_details;
                    $sf = DB::table('student_fees')->where('id', $decoded[0]['student_fee_id'])->first();
                    if ($sf) {
                        $instNo = $sf->installment_no;
                    }
                }
            }

            // Create original payment invoice
            $paymentId = DB::table('fee_invoices')->insertGetId([
                'school_id' => $receipt->school_id,
                'student_id' => $receipt->student_id,
                'invoice_number' => $receipt->receipt_number,
                'installment_no' => $instNo,
                'type' => 'payment',
                'status' => ($receipt->status === 'cancelled') ? 'cancelled' : 'paid',
                'amount' => $receipt->amount_paid,
                'discount_amount' => $receipt->discount_amount ?? 0.00,
                'payment_mode' => $receipt->payment_mode,
                'payment_date' => $receipt->payment_date,
                'payment_details' => $details,
                'created_at' => $receipt->created_at,
                'updated_at' => $receipt->updated_at,
            ]);

            // If it was cancelled, create the distinct cancellation invoice
            if ($receipt->status === 'cancelled') {
                DB::table('fee_invoices')->insert([
                    'school_id' => $receipt->school_id,
                    'student_id' => $receipt->student_id,
                    'invoice_number' => 'INV-' . $instNo . '-CNL-' . rand(1000, 9999) . '-' . time(),
                    'related_invoice_id' => $paymentId,
                    'related_invoice_number' => $receipt->receipt_number,
                    'installment_no' => $instNo,
                    'type' => 'cancel_payment',
                    'status' => 'cancelled',
                    'amount' => $receipt->amount_paid,
                    'discount_amount' => 0.00,
                    'payment_mode' => $receipt->payment_mode,
                    'payment_date' => $receipt->payment_date,
                    'remarks' => 'Migration Backfill Cancellation',
                    'created_at' => $receipt->updated_at,
                    'updated_at' => $receipt->updated_at,
                ]);
            }
        }

        // Backfill legacy refunds
        $refunds = DB::table('fee_refunds')->get();
        foreach ($refunds as $refund) {
            $instNo = 1;
            if (preg_replace('/[^0-9]/', '', $refund->reason)) {
                $instNo = (int) preg_replace('/[^0-9]/', '', $refund->reason) ?: 1;
            }

            DB::table('fee_invoices')->insert([
                'school_id' => $refund->school_id,
                'student_id' => $refund->student_id,
                'invoice_number' => $refund->slip_no,
                'installment_no' => $instNo,
                'type' => 'refund',
                'status' => 'refunded',
                'amount' => $refund->amount,
                'discount_amount' => 0.00,
                'payment_mode' => $refund->payment_mode,
                'payment_date' => $refund->refund_date,
                'remarks' => $refund->reason,
                'created_at' => $refund->created_at,
                'updated_at' => $refund->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_invoices');
    }
};
