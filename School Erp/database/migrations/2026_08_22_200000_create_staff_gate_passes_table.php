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
        Schema::create('staff_gate_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();

            $table->string('gate_pass_number', 50)->index();
            $table->dateTime('pass_date')->index();
            $table->string('template', 30)->default('classic'); // classic, modern, minimal, portrait, landscape

            // Reason and notes
            $table->string('reason', 255);
            $table->string('expected_return_time', 50)->nullable();
            $table->dateTime('actual_return_time')->nullable();

            // Status: draft, generated, issued, returned, cancelled
            $table->string('status', 30)->default('issued')->index();
            $table->text('remarks')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Authorizations
            $table->string('approved_by', 100)->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();

            // Snapshots for audit & offline integrity
            $table->string('school_name', 255)->nullable();
            $table->string('school_logo', 255)->nullable();
            $table->string('school_address', 255)->nullable();
            $table->string('school_city', 100)->nullable();
            $table->string('school_state', 100)->nullable();
            $table->string('school_pincode', 20)->nullable();
            $table->string('school_phone', 50)->nullable();
            $table->string('school_email', 100)->nullable();

            $table->json('staff_snapshot')->nullable();
            $table->json('meta_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'pass_date']);
            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_gate_passes');
    }
};
