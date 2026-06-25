<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->string('local_name')->nullable();
            $table->string('class_code')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->string('local_name')->nullable();
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropColumn(['local_name', 'class_code', 'sort_order']);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['local_name', 'sort_order']);
        });
    }
};
