<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_cheques', 'branch')) {
                $table->string('branch')->nullable();
            }
            if (!Schema::hasColumn('pending_cheques', 'installment_no')) {
                $table->string('installment_no')->nullable();
            }
            if (!Schema::hasColumn('pending_cheques', 'receipt_number')) {
                $table->string('receipt_number')->nullable();
            }
            if (!Schema::hasColumn('pending_cheques', 'entry_date')) {
                $table->date('entry_date')->nullable();
            }
            if (!Schema::hasColumn('pending_cheques', 'receipt_date')) {
                $table->date('receipt_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            $table->dropColumn(['branch', 'installment_no', 'receipt_number', 'entry_date', 'receipt_date']);
        });
    }
};
