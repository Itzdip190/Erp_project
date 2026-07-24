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
                if (!Schema::hasColumn('report_card_histories', 'passed_result_text')) {
                    $table->text('passed_result_text')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'failed_result_text')) {
                    $table->text('failed_result_text')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'passed_promoted_text')) {
                    $table->text('passed_promoted_text')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'failed_promoted_text')) {
                    $table->text('failed_promoted_text')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'positions_show_till')) {
                    $table->integer('positions_show_till')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'failed_position_text')) {
                    $table->text('failed_position_text')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'absent_position_text')) {
                    $table->text('absent_position_text')->nullable();
                }
                if (!Schema::hasColumn('report_card_histories', 'medical_position_text')) {
                    $table->text('medical_position_text')->nullable();
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
                $columns = [
                    'passed_result_text',
                    'failed_result_text',
                    'passed_promoted_text',
                    'failed_promoted_text',
                    'positions_show_till',
                    'failed_position_text',
                    'absent_position_text',
                    'medical_position_text',
                ];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('report_card_histories', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
