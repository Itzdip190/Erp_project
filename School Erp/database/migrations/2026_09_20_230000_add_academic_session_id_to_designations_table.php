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
        Schema::table('designations', function (Blueprint $table) {
            if (!Schema::hasColumn('designations', 'academic_session_id')) {
                $table->foreignId('academic_session_id')
                    ->nullable()
                    ->after('system_role')
                    ->constrained('academic_sessions')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designations', function (Blueprint $table) {
            if (Schema::hasColumn('designations', 'academic_session_id')) {
                $table->dropForeign(['academic_session_id']);
                $table->dropColumn('academic_session_id');
            }
        });
    }
};
