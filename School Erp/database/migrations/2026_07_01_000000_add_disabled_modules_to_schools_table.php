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
        if (Schema::hasTable('schools') && !Schema::hasColumn('schools', 'disabled_modules')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->text('disabled_modules')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('schools') && Schema::hasColumn('schools', 'disabled_modules')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('disabled_modules');
            });
        }
    }
};
