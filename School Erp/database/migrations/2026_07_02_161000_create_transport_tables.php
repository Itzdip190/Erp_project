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
        // 1. Vehicles table
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_no', 50);
            $table->string('vehicle_model', 100)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 30)->nullable();
            $table->integer('capacity')->default(40);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. Stops table
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name', 150);
            $table->string('landmark', 150)->nullable();
            $table->decimal('fare', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Transport Routes table
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // 4. Vehicle Trips table
        Schema::create('vehicle_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->string('trip_name', 100);
            $table->string('type', 50)->default('pickup'); // pickup, drop, both
            $table->string('start_time', 20)->nullable();
            $table->string('end_time', 20)->nullable();
            $table->timestamps();
        });

        // 5. Bus Attendances table
        Schema::create('bus_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('trip_type', 50)->default('pickup'); // pickup, drop
            $table->string('status', 30)->default('present'); // present, absent
            $table->timestamps();
        });

        // 6. Vehicle Expenses table
        Schema::create('vehicle_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('expense_type', 100);
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_expenses');
        Schema::dropIfExists('bus_attendances');
        Schema::dropIfExists('vehicle_trips');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('stops');
        Schema::dropIfExists('vehicles');
    }
};
