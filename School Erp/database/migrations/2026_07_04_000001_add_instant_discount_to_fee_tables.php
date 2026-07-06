<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add instant discount fields to student_fees
        if (!Schema::hasColumn('student_fees', 'instant_discount_amount')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $table->decimal('instant_discount_amount', 10, 2)->default(0)->after('paid_amount');
                $table->string('instant_discount_type', 20)->nullable()->after('instant_discount_amount'); // 'percentage' or 'flat'
            });
        }

        // Add discount_amount to fee_receipts
        if (!Schema::hasColumn('fee_receipts', 'discount_amount')) {
            Schema::table('fee_receipts', function (Blueprint $table) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('amount_paid');
                $table->string('discount_type', 20)->nullable()->after('discount_amount');
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropColumn(['instant_discount_amount', 'instant_discount_type']);
        });
        Schema::table('fee_receipts', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'discount_type']);
        });
    }
};
