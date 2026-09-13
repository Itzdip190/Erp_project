<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_structures')) {
            Schema::create('salary_structures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->decimal('basic_salary', 12, 2)->default(0);
                $table->string('salary_type', 50)->default('Monthly'); // Monthly, Daily, Hourly, Contract
                $table->decimal('hra', 12, 2)->default(0);
                $table->decimal('da', 12, 2)->default(0);
                $table->decimal('ta', 12, 2)->default(0);
                $table->decimal('allowance', 12, 2)->default(0);
                $table->decimal('pf', 12, 2)->default(0);
                $table->decimal('esi', 12, 2)->default(0);
                $table->decimal('tds', 12, 2)->default(0);
                $table->decimal('prof_tax', 12, 2)->default(0);
                $table->date('effective_from')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['school_id', 'is_active']);
            });
        }

        if (Schema::hasTable('staff') && !Schema::hasColumn('staff', 'salary_structure_id')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->unsignedBigInteger('salary_structure_id')->nullable()->after('basic_salary');
                $table->index('salary_structure_id');
            });
        }

        if (Schema::hasTable('staff_salary_structures') && !Schema::hasColumn('staff_salary_structures', 'salary_structure_id')) {
            Schema::table('staff_salary_structures', function (Blueprint $table) {
                $table->unsignedBigInteger('salary_structure_id')->nullable()->after('staff_id');
                $table->index('salary_structure_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('staff_salary_structures') && Schema::hasColumn('staff_salary_structures', 'salary_structure_id')) {
            Schema::table('staff_salary_structures', function (Blueprint $table) {
                $table->dropColumn('salary_structure_id');
            });
        }

        if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'salary_structure_id')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('salary_structure_id');
            });
        }

        Schema::dropIfExists('salary_structures');
    }
};
