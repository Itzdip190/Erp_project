<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff_leave_applications')) {
            if (!Schema::hasColumn('staff_leave_applications', 'deleted_at')) {
                Schema::table('staff_leave_applications', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        if (Schema::hasTable('leave_types')) {
            if (!Schema::hasColumn('leave_types', 'deleted_at')) {
                Schema::table('leave_types', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        if (Schema::hasTable('staff_leave_balances')) {
            if (!Schema::hasColumn('staff_leave_balances', 'deleted_at')) {
                Schema::table('staff_leave_balances', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff_leave_applications') && Schema::hasColumn('staff_leave_applications', 'deleted_at')) {
            Schema::table('staff_leave_applications', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('leave_types') && Schema::hasColumn('leave_types', 'deleted_at')) {
            Schema::table('leave_types', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('staff_leave_balances') && Schema::hasColumn('staff_leave_balances', 'deleted_at')) {
            Schema::table('staff_leave_balances', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
