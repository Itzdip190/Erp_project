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
        Schema::table('misc_fees', function (Blueprint $table) {
            $table->string('fee_head_name')->nullable()->after('academic_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('misc_fees', function (Blueprint $table) {
            $table->dropColumn('fee_head_name');
        });
    }
};
