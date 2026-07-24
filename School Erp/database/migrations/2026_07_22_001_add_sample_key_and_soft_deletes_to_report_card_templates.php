<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_card_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('report_card_templates', 'sample_template_key')) {
                $table->string('sample_template_key')->nullable()->default('sample_1')->after('content');
            }
            if (!Schema::hasColumn('report_card_templates', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('report_card_template_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('report_card_template_mappings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_card_templates', function (Blueprint $table) {
            if (Schema::hasColumn('report_card_templates', 'sample_template_key')) {
                $table->dropColumn('sample_template_key');
            }
            if (Schema::hasColumn('report_card_templates', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('report_card_template_mappings', function (Blueprint $table) {
            if (Schema::hasColumn('report_card_template_mappings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
