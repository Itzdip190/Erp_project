<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fee_visible to students table (for student-wise fee visibility toggle)
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'fee_visible')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('fee_visible')->default(true)->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'fee_visible')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('fee_visible');
            });
        }
    }
};
