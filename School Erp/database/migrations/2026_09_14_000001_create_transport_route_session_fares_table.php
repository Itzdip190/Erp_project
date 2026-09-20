<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transport_route_session_fares')) {
            Schema::create('transport_route_session_fares', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
                $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
                $table->foreignId('stop_id')->nullable()->constrained('stops')->onDelete('cascade');
                $table->decimal('pick_fare', 10, 2)->default(0.00);
                $table->decimal('drop_fare', 10, 2)->default(0.00);
                $table->timestamps();

                $table->index(['school_id', 'academic_session_id', 'route_id'], 'tr_sf_school_session_route_idx');
                $table->index(['route_id', 'stop_id', 'academic_session_id'], 'tr_sf_route_stop_session_idx');
            });

            // Seed initial session fares from existing transport_routes and route_stops
            try {
                if (Schema::hasTable('schools') && Schema::hasTable('academic_sessions') && Schema::hasTable('transport_routes')) {
                    $schools = DB::table('schools')->select('id')->get();
                    foreach ($schools as $school) {
                        $sessions = DB::table('academic_sessions')->where('school_id', $school->id)->get();
                        $routes = DB::table('transport_routes')->where('school_id', $school->id)->get();

                        foreach ($sessions as $session) {
                            foreach ($routes as $route) {
                                // Default route-level fare
                                DB::table('transport_route_session_fares')->insertOrIgnore([
                                    'school_id'           => $school->id,
                                    'academic_session_id' => $session->id,
                                    'route_id'            => $route->id,
                                    'stop_id'             => null,
                                    'pick_fare'           => $route->pick_fare ?? 0.00,
                                    'drop_fare'           => $route->drop_fare ?? 0.00,
                                    'created_at'          => now(),
                                    'updated_at'          => now(),
                                ]);

                                // Route stops fares
                                if (Schema::hasTable('route_stops')) {
                                    $routeStops = DB::table('route_stops')
                                        ->where('school_id', $school->id)
                                        ->where('route_id', $route->id)
                                        ->get();

                                    foreach ($routeStops as $rs) {
                                        DB::table('transport_route_session_fares')->insertOrIgnore([
                                            'school_id'           => $school->id,
                                            'academic_session_id' => $session->id,
                                            'route_id'            => $route->id,
                                            'stop_id'             => $rs->stop_id,
                                            'pick_fare'           => $rs->pick_fare ?? 0.00,
                                            'drop_fare'           => $rs->drop_fare ?? 0.00,
                                            'created_at'          => now(),
                                            'updated_at'          => now(),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore seeding errors during migration if tables are empty
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_route_session_fares');
    }
};
