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
        if (Schema::hasTable('student_deletion_requests')) {
            Schema::table('student_deletion_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('student_deletion_requests', 'academic_session_id')) {
                    $table->unsignedBigInteger('academic_session_id')->nullable()->after('student_id');
                    $table->index(['school_id', 'academic_session_id']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_deletion_requests')) {
            Schema::table('student_deletion_requests', function (Blueprint $table) {
                if (Schema::hasColumn('student_deletion_requests', 'academic_session_id')) {
                    $table->dropIndex(['school_id', 'academic_session_id']);
                    $table->dropColumn('academic_session_id');
                }
            });
        }
    }
};
