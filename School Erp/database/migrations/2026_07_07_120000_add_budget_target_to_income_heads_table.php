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
        Schema::table('income_heads', function (Blueprint $table) {
            $table->decimal('budget_target', 12, 2)->default(0.00)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_heads', function (Blueprint $table) {
            $table->dropColumn('budget_target');
        });
    }
};
