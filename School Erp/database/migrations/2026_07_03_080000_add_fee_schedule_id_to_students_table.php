<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'fee_schedule_id')) {
                $table->foreignId('fee_schedule_id')
                    ->nullable()
                    ->after('academic_session_id')
                    ->constrained('fee_schedules')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'fee_schedule_id')) {
                $table->dropForeign(['fee_schedule_id']);
                $table->dropColumn('fee_schedule_id');
            }
        });
    }
};
