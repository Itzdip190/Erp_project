<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_configurations', function (Blueprint $table) {
            $table->string('transport_invoice_title')->default('Transport Invoice')->after('invoice_title');
        });
    }

    public function down(): void
    {
        Schema::table('fee_configurations', function (Blueprint $table) {
            $table->dropColumn('transport_invoice_title');
        });
    }
};
