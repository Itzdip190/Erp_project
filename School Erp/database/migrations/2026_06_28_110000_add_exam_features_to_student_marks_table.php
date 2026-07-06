<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            if (!Schema::hasColumn('student_marks', 'attendance_status')) {
                $table->string('attendance_status')->default('present')->after('remarks'); // present, absent
            }
            if (!Schema::hasColumn('student_marks', 'achievements')) {
                $table->text('achievements')->nullable()->after('attendance_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropColumn(['attendance_status', 'achievements']);
        });
    }
};
