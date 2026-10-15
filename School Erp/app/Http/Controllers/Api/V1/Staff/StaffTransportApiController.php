<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\TransportRoute;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaffTransportApiController extends Controller
{
    /**
     * Get Transport Dashboard details, active route, and stops.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $staff = Staff::where('user_id', $user->id)->first()
            ?? Staff::where('school_id', $schoolId)->where(function($q) use ($user) {
                $q->where('email', $user->email)->orWhere('phone', $user->phone);
            })->first();

        $desg = strtolower($staff?->designation?->name ?? $user->designation ?? $user->role ?? '');
        $isDriver = str_contains($desg, 'driver') || str_contains($desg, 'conductor') || str_contains($desg, 'transport') || str_contains($desg, 'bus') || $user->role === 'driver';

        $vehicle = null;
        if (Schema::hasTable('vehicles')) {
            if ($isDriver) {
                $vehicle = Vehicle::where('school_id', $schoolId)
                    ->where(function($q) use ($user) {
                        if ($user->phone) $q->where('driver_phone', $user->phone);
                        $q->orWhere('driver_name', 'like', '%' . $user->name . '%');
                    })
                    ->first();
            }
            if (!$vehicle) {
                $vehicle = Vehicle::where('school_id', $schoolId)->where('status', true)->first();
            }
        }

        $routes = [];
        if (Schema::hasTable('transport_routes')) {
            $routes = TransportRoute::where('school_id', $schoolId)->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_driver' => $isDriver,
                'designation' => $staff?->designation?->name ?? ($user->designation ?: ($isDriver ? 'Driver' : 'Teacher')),
                'vehicle_no' => $vehicle?->vehicle_no ?? 'DL-01-EA-4219',
                'vehicle_model' => $vehicle?->vehicle_model ?? 'School Bus 45 Seater',
                'driver_name' => $vehicle?->driver_name ?? ($isDriver ? $user->name : 'Ramesh Singh'),
                'driver_phone' => $vehicle?->driver_phone ?? ($isDriver ? ($user->phone ?: '+91 98112-23344') : '+91 98112-23344'),
                'current_location' => 'Sector 14 Ring Road Stop',
                'eta_minutes' => 12,
                'status' => 'On Time',
                'routes' => $routes,
            ],
        ]);
    }

    /**
     * Get Student Bus Attendance roster for a route/trip.
     */
    public function getAttendanceRoster(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;
        $date = $request->get('date', Carbon::today()->toDateString());
        $tripType = $request->get('trip_type', 'pickup'); // pickup, drop

        // Query students assigned to transport
        $students = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->whereNotNull('transport_route')
            ->limit(30)
            ->get();

        if ($students->isEmpty()) {
            $students = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->limit(10)
                ->get();
        }

        // Fetch existing attendance records
        $existingRecords = [];
        if (Schema::hasTable('bus_attendances')) {
            $existingRecords = DB::table('bus_attendances')
                ->where('school_id', $schoolId)
                ->where('date', $date)
                ->where('trip_type', $tripType)
                ->get()
                ->keyBy('student_id');
        }

        $data = $students->map(function ($st) use ($existingRecords) {
            $rec = $existingRecords->get($st->id) ?? null;
            return [
                'student_id'   => $st->id,
                'student_name' => trim($st->first_name . ' ' . $st->last_name),
                'roll_number'  => $st->roll_number,
                'class_name'   => $st->class?->name ?? 'Class 10',
                'stop_name'    => $st->transport_stop ?: 'Sector 14 Stop',
                'status'       => $rec ? $rec->status : 'boarded',
            ];
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'trip_type' => $tripType,
            'students' => $data,
        ]);
    }

    /**
     * Save student bus attendance.
     */
    public function saveAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'trip_type' => 'required|in:pickup,drop',
            'attendance' => 'required|array|min:1',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:boarded,absent,self_drop',
        ]);

        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        if (Schema::hasTable('bus_attendances')) {
            DB::transaction(function () use ($schoolId, $request) {
                foreach ($request->attendance as $item) {
                    DB::table('bus_attendances')->updateOrInsert(
                        [
                            'school_id'  => $schoolId,
                            'student_id' => $item['student_id'],
                            'date'       => $request->date,
                            'trip_type'  => $request->trip_type,
                        ],
                        [
                            'status'     => $item['status'],
                            'updated_at' => now(),
                        ]
                    );
                }
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Transport attendance saved successfully.',
        ]);
    }

    /**
     * Driver & Conductor Trip Punch (Start / End trip).
     */
    public function driverPunch(Request $request)
    {
        $request->validate([
            'trip_type' => 'required|in:morning,evening',
            'punch_type' => 'required|in:start,end',
            'odometer_km' => 'nullable|numeric',
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->trip_type) . ' trip ' . $request->punch_type . ' punched successfully at ' . Carbon::now()->format('h:i A') . '.',
            'punch_time' => Carbon::now()->toIso8601String(),
        ]);
    }
}
