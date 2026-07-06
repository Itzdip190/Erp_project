<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'invoice_no')) {
                $table->string('invoice_no')->nullable()->after('status');
            }
            if (!Schema::hasColumn('student_fees', 'fee_schedule_id')) {
                $table->foreignId('fee_schedule_id')->nullable()->after('fee_category_id')->constrained('fee_schedules')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['fee_schedule_id']);
            $table->dropColumn(['invoice_no', 'fee_schedule_id']);
        });
    }
};
