<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add attachment field to vehicle_expenses for receipt verification
        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->string('attachment', 500)->nullable()->after('description');
        });

        // Add split fare fields to stops table (pick_fare and drop_fare separate)
        if (Schema::hasTable('stops')) {
            Schema::table('stops', function (Blueprint $table) {
                if (!Schema::hasColumn('stops', 'pick_fare')) {
                    $table->decimal('pick_fare', 10, 2)->default(0.00)->after('fare');
                }
                if (!Schema::hasColumn('stops', 'drop_fare')) {
                    $table->decimal('drop_fare', 10, 2)->default(0.00)->after('pick_fare');
                }
            });
        }

        // Add default fares and student count to transport_routes
        if (Schema::hasTable('transport_routes')) {
            Schema::table('transport_routes', function (Blueprint $table) {
                if (!Schema::hasColumn('transport_routes', 'pick_fare')) {
                    $table->decimal('pick_fare', 10, 2)->default(0.00)->after('description');
                }
                if (!Schema::hasColumn('transport_routes', 'drop_fare')) {
                    $table->decimal('drop_fare', 10, 2)->default(0.00)->after('pick_fare');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        Schema::table('stops', function (Blueprint $table) {
            $table->dropColumn(['pick_fare', 'drop_fare']);
        });

        Schema::table('transport_routes', function (Blueprint $table) {
            $table->dropColumn(['pick_fare', 'drop_fare']);
        });
    }
};
