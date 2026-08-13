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
        Schema::create('student_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('admission_number')->nullable();
            $table->string('student_name');
            $table->string('class_name')->nullable();
            $table->string('section_name')->nullable();
            $table->text('reason')->nullable();
            
            $table->unsignedBigInteger('requested_by');
            $table->string('requested_by_name')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->string('rejected_by_name')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_deletion_requests');
    }
};
