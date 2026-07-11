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
        // 1. Update fee_schedules
        Schema::table('fee_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_schedules', 'installment_type')) {
                $table->string('installment_type')->default('custom')->after('academic_session_id');
            }
            if (!Schema::hasColumn('fee_schedules', 'installments')) {
                $table->json('installments')->nullable()->after('no_of_installments');
            }
            if (!Schema::hasColumn('fee_schedules', 'fine_id')) {
                $table->foreignId('fine_id')->nullable()->after('installments')->constrained('fee_fines')->onDelete('set null');
            }
        });

        // 2. Update transport_fee_schedules
        Schema::table('transport_fee_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('transport_fee_schedules', 'months')) {
                $table->dropColumn('months');
            }
            if (!Schema::hasColumn('transport_fee_schedules', 'installments')) {
                $table->json('installments')->nullable()->after('name');
            }
            if (!Schema::hasColumn('transport_fee_schedules', 'installment_type')) {
                $table->string('installment_type')->default('custom')->after('academic_session_id');
            }
            if (!Schema::hasColumn('transport_fee_schedules', 'fine_id')) {
                $table->foreignId('fine_id')->nullable()->after('installments')->constrained('fee_fines')->onDelete('set null');
            }
        });

        // 3. Update student_fees
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'fine_applied_at')) {
                $table->timestamp('fine_applied_at')->nullable()->after('invoice_status');
            }
            if (!Schema::hasColumn('student_fees', 'fine_amount_applied')) {
                $table->decimal('fine_amount_applied', 10, 2)->default(0.00)->after('fine_applied_at');
            }
            if (!Schema::hasColumn('student_fees', 'transport_fee_schedule_id')) {
                $table->foreignId('transport_fee_schedule_id')->nullable()->after('fee_schedule_id')->constrained('transport_fee_schedules')->onDelete('set null');
            }
        });

        // 4. Update fee_fines
        Schema::table('fee_fines', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_fines', 'default_grace_days')) {
                $table->integer('default_grace_days')->default(0)->after('fine_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_fines', function (Blueprint $table) {
            $table->dropColumn('default_grace_days');
        });

        Schema::table('student_fees', function (Blueprint $table) {
            if (Schema::hasColumn('student_fees', 'transport_fee_schedule_id')) {
                $table->dropForeign(['transport_fee_schedule_id']);
            }
            $table->dropColumn(['fine_applied_at', 'fine_amount_applied', 'transport_fee_schedule_id']);
        });

        Schema::table('transport_fee_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('transport_fee_schedules', 'fine_id')) {
                $table->dropForeign(['fine_id']);
            }
            $table->dropColumn(['installments', 'installment_type', 'fine_id']);
            $table->json('months')->nullable()->after('name');
        });

        Schema::table('fee_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('fee_schedules', 'fine_id')) {
                $table->dropForeign(['fine_id']);
            }
            $table->dropColumn(['installment_type', 'installments', 'fine_id']);
        });
    }
};
