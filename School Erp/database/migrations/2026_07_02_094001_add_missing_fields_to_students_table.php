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
            $table->string('sub_category', 80)->nullable()->after('category_id');
            $table->boolean('any_allergy')->default(false)->after('medical_allergies');
            $table->string('birthmark', 150)->nullable()->after('gender');
            $table->string('house_number', 50)->nullable()->after('address_line_2');
            $table->string('location', 150)->nullable()->after('address_line_2');
            $table->string('emergency_name', 150)->nullable()->after('emergency_address');
            $table->string('emergency_number', 30)->nullable()->after('emergency_name');
            $table->string('admission_type', 80)->nullable()->after('admission_date');
            $table->string('boarding_type', 80)->nullable()->after('admission_type');
            $table->boolean('defence_personal')->default(false)->after('boarding_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'sub_category',
                'any_allergy',
                'birthmark',
                'house_number',
                'location',
                'emergency_name',
                'emergency_number',
                'admission_type',
                'boarding_type',
                'defence_personal',
            ]);
        });
    }
};
