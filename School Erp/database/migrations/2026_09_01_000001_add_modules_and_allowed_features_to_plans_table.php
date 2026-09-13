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
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'modules')) {
                $table->json('modules')->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('plans', 'allowed_features')) {
                $table->json('allowed_features')->nullable()->after('modules');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'allowed_features')) {
                $table->dropColumn('allowed_features');
            }
            if (Schema::hasColumn('plans', 'modules')) {
                $table->dropColumn('modules');
            }
        });
    }
};
