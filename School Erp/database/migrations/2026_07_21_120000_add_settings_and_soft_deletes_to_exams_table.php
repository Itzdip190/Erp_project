<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'enable_doj_eligibility')) {
                $table->boolean('enable_doj_eligibility')->default(false)->after('description');
            }
            if (!Schema::hasColumn('exams', 'enable_auto_schedule')) {
                $table->boolean('enable_auto_schedule')->default(false)->after('enable_doj_eligibility');
            }
            if (!Schema::hasColumn('exams', 'portal_open_at')) {
                $table->dateTime('portal_open_at')->nullable()->after('enable_auto_schedule');
            }
            if (!Schema::hasColumn('exams', 'portal_close_at')) {
                $table->dateTime('portal_close_at')->nullable()->after('portal_open_at');
            }
            if (!Schema::hasColumn('exams', 'teacher_permissions')) {
                $table->json('teacher_permissions')->nullable()->after('portal_close_at');
            }
            if (!Schema::hasColumn('exams', 'deleted_at')) {
                $table->softDeletes()->after('teacher_permissions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'enable_doj_eligibility',
                'enable_auto_schedule',
                'portal_open_at',
                'portal_close_at',
                'teacher_permissions',
                'deleted_at',
            ]);
        });
    }
};
