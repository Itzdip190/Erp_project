<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_wise_fee_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->cascadeOnDelete();
            $table->foreignId('fee_schedule_id')->constrained('fee_schedules')->cascadeOnDelete();
            $table->foreignId('student_category_id')->constrained('student_categories')->cascadeOnDelete();
            $table->foreignId('fee_component_id')->constrained('fee_components')->cascadeOnDelete();
            $table->boolean('status')->default(false);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->json('installment_amounts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_wise_fee_allocations');
    }
};
