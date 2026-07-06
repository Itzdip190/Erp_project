<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            if (!Schema::hasColumn('pending_cheques', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            if (Schema::hasColumn('pending_cheques', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });
    }
};
