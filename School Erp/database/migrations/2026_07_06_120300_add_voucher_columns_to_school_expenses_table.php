<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_voucher_id')->nullable()->after('school_id');
            $table->unsignedBigInteger('voucher_payment_id')->nullable()->after('expense_voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('school_expenses', function (Blueprint $table) {
            $table->dropColumn(['expense_voucher_id', 'voucher_payment_id']);
        });
    }
};
