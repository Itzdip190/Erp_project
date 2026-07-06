<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_wise_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
            $table->foreignId('fee_schedule_id')->constrained('fee_schedules')->onDelete('cascade');
            $table->foreignId('student_category_id')->constrained('student_categories')->onDelete('cascade');
            $table->foreignId('fee_component_id')->constrained('fee_components')->onDelete('cascade');
            $table->boolean('is_active')->default(false);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->json('installments')->nullable(); // Stores installment amounts and dates
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_wise_fees');
    }
};
