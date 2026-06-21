<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impl_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('integration_name');
            $table->string('company')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('vendor_contact_details')->nullable();
            $table->dateTime('api_received_on')->nullable();
            $table->dateTime('implemented_on')->nullable();
            $table->string('tat')->nullable();
            $table->string('owner_school_side')->nullable();
            $table->string('confirmation_school_side')->nullable();
            $table->string('status')->default('Pending');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impl_integrations');
    }
};
