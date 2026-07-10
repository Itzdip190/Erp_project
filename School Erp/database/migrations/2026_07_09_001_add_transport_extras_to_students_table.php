<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Transport opted flag — KEY field for fee applicability
            $table->boolean('transport_opted')->default(false)->after('transport_drop_vehicle_code');

            // Proper FK to transport_routes (nullable)
            $table->unsignedBigInteger('transport_route_id')->nullable()->after('transport_opted');

            // Per-student fares (override stop-level fare)
            $table->decimal('transport_pick_fare', 10, 2)->nullable()->after('transport_route_id');
            $table->decimal('transport_drop_fare', 10, 2)->nullable()->after('transport_pick_fare');

            // Custom pickup & drop locations
            $table->string('transport_pickup_location', 255)->nullable()->after('transport_drop_fare');
            $table->string('transport_drop_location', 255)->nullable()->after('transport_pickup_location');

            // Scheduled times
            $table->string('transport_pickup_time', 20)->nullable()->after('transport_drop_location');
            $table->string('transport_drop_time', 20)->nullable()->after('transport_pickup_time');

            // Calendar start date
            $table->date('transport_calendar_start')->nullable()->after('transport_drop_time');

            // Add index for transport route FK
            $table->index('transport_route_id', 'students_transport_route_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_transport_route_id_idx');
            $table->dropColumn([
                'transport_opted',
                'transport_route_id',
                'transport_pick_fare',
                'transport_drop_fare',
                'transport_pickup_location',
                'transport_drop_location',
                'transport_pickup_time',
                'transport_drop_time',
                'transport_calendar_start',
            ]);
        });
    }
};
