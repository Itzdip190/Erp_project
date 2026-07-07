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
        Schema::table('schools', function (Blueprint $table) {
            $table->string('state', 10)->nullable()->after('code');
            $table->string('school_type', 50)->nullable()->after('state');
            $table->string('director_name', 255)->nullable()->after('school_type');
        });

        Schema::table('school_requests', function (Blueprint $table) {
            $table->string('state', 10)->nullable()->after('code');
            $table->string('school_type', 50)->nullable()->after('state');
            $table->string('director_name', 255)->nullable()->after('school_type');
            $table->string('email', 255)->nullable()->after('phone');
            $table->string('academic_session_name', 100)->nullable()->after('plan_id');
            $table->date('academic_session_start_date')->nullable()->after('academic_session_name');
            $table->date('academic_session_end_date')->nullable()->after('academic_session_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['state', 'school_type', 'director_name']);
        });

        Schema::table('school_requests', function (Blueprint $table) {
            $table->dropColumn([
                'state',
                'school_type',
                'director_name',
                'email',
                'academic_session_name',
                'academic_session_start_date',
                'academic_session_end_date'
            ]);
        });
    }
};
