<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_expenses', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_mode');
            $table->date('check_issue_date')->nullable()->after('bank_name');
            $table->string('branch')->nullable()->after('check_issue_date');
        });

        Schema::table('voucher_payments', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_mode');
            $table->date('check_issue_date')->nullable()->after('bank_name');
            $table->string('branch')->nullable()->after('check_issue_date');
        });
    }

    public function down(): void
    {
        Schema::table('school_expenses', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'check_issue_date', 'branch']);
        });

        Schema::table('voucher_payments', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'check_issue_date', 'branch']);
        });
    }
};
