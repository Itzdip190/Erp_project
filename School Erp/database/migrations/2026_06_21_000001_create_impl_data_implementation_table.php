<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impl_data_implementation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('module_name');
            $table->text('attachments')->nullable(); // Stored as JSON array/string
            $table->string('uploaded_by')->nullable();
            $table->dateTime('data_received_date')->nullable();
            $table->dateTime('data_implemented_on')->nullable();
            $table->string('tat')->nullable();
            $table->string('owner_school_side')->nullable(); // Staff name or ID
            $table->string('confirmation_school_side')->nullable(); // Confirmed / Not Confirmed
            $table->string('status')->default('Pending'); // Pending / In Progress / Completed
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impl_data_implementation');
    }
};
