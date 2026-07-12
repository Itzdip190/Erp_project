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
        Schema::create('pending_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('type'); // schedule, component, discount, misc_fee, fine
            $table->unsignedBigInteger('target_id');
            $table->string('item_name'); // e.g. "SOUHARDYADIP MONDAL Schedule"
            $table->string('requested_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_deletions');
    }
};
