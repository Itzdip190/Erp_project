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
        if (Schema::hasTable('student_fees')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $hasIndex1 = method_exists(Schema::class, 'hasIndex') 
                    ? Schema::hasIndex('student_fees', 'idx_sf_school_student_inst') 
                    : false;

                $hasIndex2 = method_exists(Schema::class, 'hasIndex') 
                    ? Schema::hasIndex('student_fees', 'idx_sf_school_student_status') 
                    : false;

                if (!$hasIndex1) {
                    try {
                        $table->index(['school_id', 'student_id', 'installment_no'], 'idx_sf_school_student_inst');
                    } catch (\Throwable $e) {
                        // Index might already exist
                    }
                }

                if (!$hasIndex2) {
                    try {
                        $table->index(['school_id', 'student_id', 'status'], 'idx_sf_school_student_status');
                    } catch (\Throwable $e) {
                        // Index might already exist
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_fees')) {
            Schema::table('student_fees', function (Blueprint $table) {
                try {
                    $table->dropIndex('idx_sf_school_student_inst');
                } catch (\Throwable $e) {}
                try {
                    $table->dropIndex('idx_sf_school_student_status');
                } catch (\Throwable $e) {}
            });
        }
    }
};
