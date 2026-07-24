<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('staff_leave_applications')) {
            Schema::create('staff_leave_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
                $table->string('academic_year');
                $table->string('staff_type')->default('Teaching');
                $table->foreignId('leave_type_id')->nullable()->constrained('leave_types')->onDelete('set null');
                $table->string('leave_type_code')->default('CL');
                $table->string('leave_type_name')->default('Casual Leaves');
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('total_days', 8, 2)->default(1);
                $table->unsignedBigInteger('substitute_staff_id')->nullable();
                $table->string('substitute_staff_name')->nullable();
                $table->text('reason')->nullable();
                $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'academic_year', 'staff_type']);
                $table->index(['school_id', 'staff_id']);
                $table->index(['school_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_leave_applications');
    }
};
