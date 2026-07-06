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
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_session_id')->nullable();
            $table->string('name');
            $table->string('scale_basis')->default('subject'); // subject, attendance
            $table->string('type')->default('scholastic'); // scholastic, custom_subject, non_scholastic
            $table->text('applicable_classes')->nullable(); // JSON array of class IDs
            $table->text('ranges')->nullable(); // JSON array of ranges {from, to, points, grade_value, key_value, fail}
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
