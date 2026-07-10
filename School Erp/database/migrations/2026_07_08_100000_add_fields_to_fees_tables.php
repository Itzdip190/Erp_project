<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_discounts', 'type')) {
                $table->string('type')->default('flat');
            }
        });

        Schema::table('fee_refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_refunds', 'slip_no')) {
                $table->string('slip_no')->nullable();
            }
            if (!Schema::hasColumn('fee_refunds', 'payment_mode')) {
                $table->string('payment_mode')->default('cash');
            }
            if (!Schema::hasColumn('fee_refunds', 'bank_date')) {
                $table->date('bank_date')->nullable();
            }
            if (!Schema::hasColumn('fee_refunds', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_discounts', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('fee_refunds', function (Blueprint $table) {
            $table->dropColumn(['slip_no', 'payment_mode', 'bank_date', 'bank_name']);
        });
    }
};
