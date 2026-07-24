<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If the table exists but doesn't have academic_year (old left-over table), drop it so we can create it cleanly
        if (Schema::hasTable('leave_types') && !Schema::hasColumn('leave_types', 'academic_year')) {
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('staff_leave_balances');
            Schema::dropIfExists('leave_types');
            Schema::enableForeignKeyConstraints();
        }

        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->string('code'); // CL, EL, SL, RH, LWP, etc.
                $table->string('name'); // e.g. Casual Leaves
                $table->string('academic_year'); // e.g. "Apr 2025 - Mar 2026"
                $table->string('staff_type'); // e.g. Teaching
                $table->boolean('is_active')->default(true);
                $table->string('unit')->default('per_year'); // per_month, per_year
                $table->decimal('leave_count', 8, 2)->default(0);
                $table->boolean('pro_rata')->default(false);
                $table->boolean('credit_on_joining')->default(false);
                $table->boolean('non_carry_forward')->default(false);
                $table->boolean('accrue_after_month')->default(false);
                $table->boolean('allow_before_date')->default(false);
                $table->string('gender_eligibility')->default('all'); // all, male, female
                $table->integer('start_crediting_days')->default(0);
                $table->timestamps();
                
                $table->unique(['school_id', 'academic_year', 'staff_type', 'code']);
            });
        }

        if (!Schema::hasTable('staff_leave_balances')) {
            Schema::create('staff_leave_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
                $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');
                $table->decimal('allowed', 8, 2)->default(0);
                $table->decimal('availed', 8, 2)->default(0);
                $table->timestamps();
                
                $table->unique(['school_id', 'staff_id', 'leave_type_id'], 'staff_leave_balance_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('staff_leave_balances');
        Schema::dropIfExists('leave_types');
        Schema::enableForeignKeyConstraints();
    }
};
