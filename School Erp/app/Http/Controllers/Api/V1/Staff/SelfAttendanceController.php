<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SelfAttendanceController extends Controller
{
    public function punch(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;
        
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School context not resolved.',
            ], 400);
        }

        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found.',
            ], 403);
        }

        $request->validate([
            'type' => 'required|in:in,out',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $today = $now->format('Y-m-d');

        // 1. Time window validation
        $punchStart = $school->staff_punch_in_start ?? '08:00:00';
        $punchEnd = $school->staff_punch_in_end ?? '18:00:00';

        if ($currentTime < $punchStart || $currentTime > $punchEnd) {
            return response()->json([
                'success' => false,
                'message' => "Attendance punch is only allowed between {$punchStart} and {$punchEnd}.",
            ], 403);
        }

        if ($request->type === 'in') {
            // Check if already punched in
            $existing = StaffAttendance::where('school_id', $school->id)
                ->where('staff_id', $staff->id)
                ->where('date', $today)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already punched in today.',
                ], 400);
            }

            // Calculate if late
            // Assume 09:00:00 is standard shift start time
            $standardStart = Carbon::createFromFormat('H:i:s', '09:00:00');
            $diffInMinutes = $standardStart->diffInMinutes($now, false); // difference from standard start
            
            $status = 'present';
            if ($diffInMinutes > ($school->late_grace_minutes ?? 15)) {
                $status = 'late';
            }

            $attendance = StaffAttendance::create([
                'school_id' => $school->id,
                'staff_id' => $staff->id,
                'date' => $today,
                'status' => $status,
                'clock_in_at' => $currentTime,
                'attendance_type' => 'gps',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'marked_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Punched in successfully.',
                'data' => $attendance,
            ]);
        } else {
            // type === 'out'
            $attendance = StaffAttendance::where('school_id', $school->id)
                ->where('staff_id', $staff->id)
                ->where('date', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must punch in first before punching out.',
                ], 400);
            }

            if ($attendance->clock_out_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already punched out today.',
                ], 400);
            }

            $attendance->update([
                'clock_out_at' => $currentTime,
                'latitude' => $request->latitude, // update to punch-out location
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Punched out successfully.',
                'data' => $attendance,
            ]);
        }
    }
}
