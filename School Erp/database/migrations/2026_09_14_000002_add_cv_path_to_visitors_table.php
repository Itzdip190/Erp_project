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
        if (Schema::hasTable('visitors') && !Schema::hasColumn('visitors', 'cv_path')) {
            Schema::table('visitors', function (Blueprint $table) {
                $table->string('cv_path', 255)->nullable()->after('photo_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('visitors') && Schema::hasColumn('visitors', 'cv_path')) {
            Schema::table('visitors', function (Blueprint $table) {
                $table->dropColumn('cv_path');
            });
        }
    }
};
