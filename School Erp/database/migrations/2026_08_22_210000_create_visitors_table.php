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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();

            // Pass / Registration Number
            $table->string('pass_number', 50)->index();
            $table->string('visitor_type', 100)->index(); // Parent / Guardian, Vendor / Supplier, Guest, Contractor, etc.

            // Personal Profile Information
            $table->string('full_name', 150)->index();
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('mobile_number', 30)->index();
            $table->string('alternate_mobile', 30)->nullable();
            $table->string('email', 150)->nullable();

            // Address & Location Details
            $table->text('street_address')->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pincode', 20)->nullable();

            // Meeting & Destination Assignment
            $table->string('whom_to_meet_type', 100); // Management, Teacher, Admin, Student, etc.
            $table->string('host_name', 150)->nullable();
            $table->string('security_gate', 100);
            $table->unsignedInteger('entourage_count')->default(1);
            $table->string('visit_purpose', 150);
            $table->text('detailed_purpose_remarks')->nullable();

            // Verification Credentials & Photo
            $table->string('id_proof_type', 100)->nullable();
            $table->string('id_proof_number', 100)->nullable();
            $table->string('vehicle_number', 50)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->text('security_notes')->nullable();

            // Status & Check In/Out
            $table->string('status', 30)->default('checked_in')->index(); // checked_in, checked_out, expected, cancelled
            $table->dateTime('check_in_at')->nullable()->index();
            $table->dateTime('check_out_at')->nullable()->index();

            // Metadata / Snapshots for offline integrity
            $table->json('meta_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'created_at']);
            $table->index(['school_id', 'mobile_number']);
            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
