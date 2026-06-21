<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impl_training', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('module_name');
            $table->dateTime('training_done_on')->nullable();
            $table->text('training_given_to')->nullable(); // JSON array of staff IDs or names
            $table->text('minutes_of_meeting')->nullable();
            $table->text('attachments')->nullable();
            $table->string('uploaded_by')->nullable();
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
        Schema::dropIfExists('impl_training');
    }
};
