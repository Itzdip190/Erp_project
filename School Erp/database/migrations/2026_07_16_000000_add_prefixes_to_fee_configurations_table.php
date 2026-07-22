<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_configurations', function (Blueprint $table) {
            $table->string('school_fee_prefix')->default('REC');
            $table->string('transport_fee_prefix')->default('TRN');
        });
    }

    public function down(): void
    {
        Schema::table('fee_configurations', function (Blueprint $table) {
            $table->dropColumn(['school_fee_prefix', 'transport_fee_prefix']);
        });
    }
};
