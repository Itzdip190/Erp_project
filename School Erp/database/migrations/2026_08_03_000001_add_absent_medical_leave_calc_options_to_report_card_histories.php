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
        if (Schema::hasTable('report_card_histories')) {
            Schema::table('report_card_histories', function (Blueprint $table) {
                if (!Schema::hasColumn('report_card_histories', 'consider_absent')) {
                    $table->boolean('consider_absent')->default(true)->after('medical_position_text');
                }
                if (!Schema::hasColumn('report_card_histories', 'consider_medical_leave')) {
                    $table->boolean('consider_medical_leave')->default(true)->after('consider_absent');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('report_card_histories')) {
            Schema::table('report_card_histories', function (Blueprint $table) {
                if (Schema::hasColumn('report_card_histories', 'consider_absent')) {
                    $table->dropColumn('consider_absent');
                }
                if (Schema::hasColumn('report_card_histories', 'consider_medical_leave')) {
                    $table->dropColumn('consider_medical_leave');
                }
            });
        }
    }
};
