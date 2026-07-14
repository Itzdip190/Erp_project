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
        if (!Schema::hasColumn('fee_discounts', 'sections')) {
            Schema::table('fee_discounts', function (Blueprint $table) {
                $table->text('sections')->nullable()->after('classes_installments');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('fee_discounts', 'sections')) {
            Schema::table('fee_discounts', function (Blueprint $table) {
                $table->dropColumn('sections');
            });
        }
    }
};
