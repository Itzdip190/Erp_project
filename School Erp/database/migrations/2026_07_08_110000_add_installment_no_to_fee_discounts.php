<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_discounts', 'installment_no')) {
                $table->integer('installment_no')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_discounts', function (Blueprint $table) {
            $table->dropColumn('installment_no');
        });
    }
};
