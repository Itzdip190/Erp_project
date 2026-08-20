<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff_payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_payrolls', 'attendance_deduction')) {
                $table->decimal('attendance_deduction', 12, 2)->default(0.00)->after('deductions');
            }
            if (!Schema::hasColumn('staff_payrolls', 'attendance_deduction_days')) {
                $table->decimal('attendance_deduction_days', 8, 2)->default(0.00)->after('attendance_deduction');
            }
            if (!Schema::hasColumn('staff_payrolls', 'attendance_deduction_multiplier')) {
                $table->decimal('attendance_deduction_multiplier', 8, 2)->default(1.00)->after('attendance_deduction_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('staff_payrolls', 'attendance_deduction')) {
                $table->dropColumn('attendance_deduction');
            }
            if (Schema::hasColumn('staff_payrolls', 'attendance_deduction_days')) {
                $table->dropColumn('attendance_deduction_days');
            }
            if (Schema::hasColumn('staff_payrolls', 'attendance_deduction_multiplier')) {
                $table->dropColumn('attendance_deduction_multiplier');
            }
        });
    }
};
