<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_payrolls', 'salary_month')) {
                $table->string('salary_month', 20)->nullable()->after('payroll_month');
            }
            if (!Schema::hasColumn('staff_payrolls', 'salary_year')) {
                $table->integer('salary_year')->nullable()->after('salary_month');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff_payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('staff_payrolls', 'salary_month')) {
                $table->dropColumn('salary_month');
            }
            if (Schema::hasColumn('staff_payrolls', 'salary_year')) {
                $table->dropColumn('salary_year');
            }
        });
    }
};
