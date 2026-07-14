<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('fee_schedules', 'sections')) {
            Schema::table('fee_schedules', function (Blueprint $table) {
                $table->text('sections')->nullable()->after('classes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fee_schedules', 'sections')) {
            Schema::table('fee_schedules', function (Blueprint $table) {
                $table->dropColumn('sections');
            });
        }
    }
};
