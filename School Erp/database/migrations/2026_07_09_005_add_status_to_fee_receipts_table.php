<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_receipts', 'status')) {
                $table->string('status')->nullable()->default('active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('fee_receipts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
