<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'invoice_status')) {
                $table->string('invoice_status')->default('active')->after('invoice_no'); // active, cancelled
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            if (Schema::hasColumn('student_fees', 'invoice_status')) {
                $table->dropColumn('invoice_status');
            }
        });
    }
};
