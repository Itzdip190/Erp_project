<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_timetable_cells')) {
            Schema::table('class_timetable_cells', function (Blueprint $table) {
                if (!Schema::hasColumn('class_timetable_cells', 'secondary_subject_id')) {
                    $table->foreignId('secondary_subject_id')
                        ->nullable()
                        ->after('teacher_id')
                        ->constrained('subjects')
                        ->nullOnDelete();
                }

                if (!Schema::hasColumn('class_timetable_cells', 'secondary_teacher_id')) {
                    $table->unsignedBigInteger('secondary_teacher_id')
                        ->nullable()
                        ->after('secondary_subject_id');
                    $table->foreign('secondary_teacher_id')
                        ->references('id')
                        ->on('staff')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('class_timetable_cells')) {
            Schema::table('class_timetable_cells', function (Blueprint $table) {
                if (Schema::hasColumn('class_timetable_cells', 'secondary_teacher_id')) {
                    $table->dropForeign(['secondary_teacher_id']);
                    $table->dropColumn('secondary_teacher_id');
                }
                if (Schema::hasColumn('class_timetable_cells', 'secondary_subject_id')) {
                    $table->dropForeign(['secondary_subject_id']);
                    $table->dropColumn('secondary_subject_id');
                }
            });
        }
    }
};
