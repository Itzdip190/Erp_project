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
        if (!Schema::hasColumn('student_cards', 'exam_id')) {
            Schema::table('student_cards', function (Blueprint $table) {
                $table->unsignedBigInteger('exam_id')->nullable()->after('card_template_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('student_cards', 'exam_id')) {
            Schema::table('student_cards', function (Blueprint $table) {
                $table->dropColumn('exam_id');
            });
        }
    }
};
