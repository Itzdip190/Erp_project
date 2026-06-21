<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff')) {
            Schema::table('staff', function (Blueprint $table) {
                if (!Schema::hasColumn('staff', 'additional_fields')) {
                    $table->json('additional_fields')->nullable()->after('pan_number');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff')) {
            Schema::table('staff', function (Blueprint $table) {
                if (Schema::hasColumn('staff', 'additional_fields')) {
                    $table->dropColumn('additional_fields');
                }
            });
        }
    }
};
