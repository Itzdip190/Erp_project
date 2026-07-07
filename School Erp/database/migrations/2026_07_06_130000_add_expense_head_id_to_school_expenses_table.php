<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_head_id')->nullable()->after('school_id');
            $table->foreign('expense_head_id')->references('id')->on('expense_heads')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('school_expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_head_id']);
            $table->dropColumn('expense_head_id');
        });
    }
};
