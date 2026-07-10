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
        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('school_expense_id')->nullable()->after('vehicle_id');
            
            // Add foreign key constraint for data integrity
            $table->foreign('school_expense_id')
                  ->references('id')
                  ->on('school_expenses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->dropForeign(['school_expense_id']);
            $table->dropColumn('school_expense_id');
        });
    }
};
