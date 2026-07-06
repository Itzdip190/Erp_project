<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'fee_component_id')) {
                $table->foreignId('fee_component_id')->nullable()->constrained('fee_components')->onDelete('set null');
            }
            if (!Schema::hasColumn('student_fees', 'installment_no')) {
                $table->integer('installment_no')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['fee_component_id']);
            $table->dropColumn(['fee_component_id', 'installment_no']);
        });
    }
};
