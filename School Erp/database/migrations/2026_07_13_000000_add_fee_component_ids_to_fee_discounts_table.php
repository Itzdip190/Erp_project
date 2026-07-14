<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('fee_discounts', 'fee_component_ids')) {
            Schema::table('fee_discounts', function (Blueprint $table) {
                $table->text('fee_component_ids')->nullable()->after('student_ids');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fee_discounts', 'fee_component_ids')) {
            Schema::table('fee_discounts', function (Blueprint $table) {
                $table->dropColumn('fee_component_ids');
            });
        }
    }
};
