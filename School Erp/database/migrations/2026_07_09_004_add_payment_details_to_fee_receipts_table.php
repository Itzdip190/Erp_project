<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_receipts', 'payment_details')) {
                $table->text('payment_details')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('fee_receipts', 'payment_details')) {
                $table->dropColumn('payment_details');
            }
        });
    }
};
