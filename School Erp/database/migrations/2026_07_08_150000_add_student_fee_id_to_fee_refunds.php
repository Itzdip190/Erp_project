<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FeeRefund;
use App\Models\StudentFee;

return new class extends Migration
{
    public function up(): void
    {
        // Add student_fee_id to fee_refunds for proper linkage
        Schema::table('fee_refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_refunds', 'student_fee_id')) {
                $table->unsignedBigInteger('student_fee_id')->nullable()->after('student_id');
            }
        });

        // ── Repair existing refunded StudentFees ──────────────────────────
        // Fees that were refunded have paid_amount=0 but syncStudentFees may have
        // reset their status back to 'pending'. We detect them by matching refund
        // records whose reason field encodes the installment number.
        // Pattern stored by process_refund:
        //   "...reason... (Refunded: ComponentName - Installment N)"
        $allRefunds = FeeRefund::all();

        foreach ($allRefunds as $refund) {
            // Extract installment number from reason, e.g. "... Installment 2)"
            if (preg_match('/Installment\s+(\d+)\)/', $refund->reason ?? '', $matches)) {
                $installmentNo = (int) $matches[1];

                // Find a StudentFee for this student+installment with paid_amount=0
                // that was likely refunded (status should be 'refunded' but got reset)
                StudentFee::where('student_id', $refund->student_id)
                    ->where('school_id', $refund->school_id)
                    ->where('installment_no', $installmentNo)
                    ->where('paid_amount', 0)
                    ->where('status', 'pending') // only fix incorrectly-reset ones
                    ->update(['status' => 'refunded']);
            }
        }
    }

    public function down(): void
    {
        Schema::table('fee_refunds', function (Blueprint $table) {
            if (Schema::hasColumn('fee_refunds', 'student_fee_id')) {
                $table->dropColumn('student_fee_id');
            }
        });
    }
};
