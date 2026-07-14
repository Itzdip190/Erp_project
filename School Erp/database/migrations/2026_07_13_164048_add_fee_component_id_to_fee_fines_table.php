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
        Schema::table('fee_fines', function (Blueprint $table) {
            $table->foreignId('fee_component_id')->nullable()->after('academic_session_id')->constrained('fee_components')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_fines', function (Blueprint $table) {
            $table->dropForeign(['fee_component_id']);
            $table->dropColumn('fee_component_id');
        });
    }
};
