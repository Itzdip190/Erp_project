<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add student_fee_ids (JSON) to pending_cheques table so the cheque
     * clearance flow can precisely credit the right StudentFee records instead
     * of guessing by installment_no.
     */
    public function up(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_cheques', 'student_fee_ids')) {
                $table->text('student_fee_ids')->nullable()->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            if (Schema::hasColumn('pending_cheques', 'student_fee_ids')) {
                $table->dropColumn('student_fee_ids');
            }
        });
    }
};
