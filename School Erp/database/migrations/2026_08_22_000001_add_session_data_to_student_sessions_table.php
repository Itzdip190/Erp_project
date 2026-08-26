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
        if (Schema::hasTable('student_sessions') && !Schema::hasColumn('student_sessions', 'session_data')) {
            Schema::table('student_sessions', function (Blueprint $table) {
                $table->json('session_data')->nullable()->after('is_promoted');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_sessions') && Schema::hasColumn('student_sessions', 'session_data')) {
            Schema::table('student_sessions', function (Blueprint $table) {
                $table->dropColumn('session_data');
            });
        }
    }
};
