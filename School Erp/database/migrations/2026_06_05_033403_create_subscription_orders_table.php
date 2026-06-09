<?php
// database/migrations/2026_06_05_033403_create_subscription_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('plan_id');
            $table->decimal('amount', 10, 2);
            $table->string('gateway'); // stripe, razorpay, paypal, bank_transfer
            $table->string('status')->default('completed'); // completed, pending, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_orders');
    }
};
