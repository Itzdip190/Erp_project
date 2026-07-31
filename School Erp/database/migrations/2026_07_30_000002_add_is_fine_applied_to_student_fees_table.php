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
        if (Schema::hasTable('student_fees')) {
            Schema::table('student_fees', function (Blueprint $table) {
                if (!Schema::hasColumn('student_fees', 'is_fine_applied')) {
                    $table->boolean('is_fine_applied')->default(true)->after('fine_amount_applied');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_fees')) {
            Schema::table('student_fees', function (Blueprint $table) {
                if (Schema::hasColumn('student_fees', 'is_fine_applied')) {
                    $table->dropColumn('is_fine_applied');
                }
            });
        }
    }
};
