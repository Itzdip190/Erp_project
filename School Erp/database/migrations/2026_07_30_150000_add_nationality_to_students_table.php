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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'nationality')) {
                $table->string('nationality', 100)->nullable()->default('Indian')->after('religion');
            }
            if (!Schema::hasColumn('students', 'permanent_house_number')) {
                $table->string('permanent_house_number', 50)->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('students', 'permanent_location')) {
                $table->string('permanent_location', 150)->nullable()->after('permanent_house_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('students', 'nationality')) {
                $columnsToDrop[] = 'nationality';
            }
            if (Schema::hasColumn('students', 'permanent_house_number')) {
                $columnsToDrop[] = 'permanent_house_number';
            }
            if (Schema::hasColumn('students', 'permanent_location')) {
                $columnsToDrop[] = 'permanent_location';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
