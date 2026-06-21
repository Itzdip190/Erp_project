<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impl_template_implementation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('template_name');
            $table->date('important_dates')->nullable();
            $table->text('template_received_attachment')->nullable();
            $table->string('uploaded_by_1')->nullable();
            $table->dateTime('template_received_on')->nullable();
            $table->text('implemented_template_attachment')->nullable();
            $table->string('uploaded_by_2')->nullable();
            $table->dateTime('template_implemented_on')->nullable();
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
        Schema::dropIfExists('impl_template_implementation');
    }
};
