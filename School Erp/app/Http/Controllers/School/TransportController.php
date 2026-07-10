<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Stop;
use App\Models\TransportRoute;
use App\Models\VehicleTrip;
use App\Models\BusAttendance;
use App\Models\VehicleExpense;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransportController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // BASICS / DASHBOARD
    // ─────────────────────────────────────────────────────────────

    public function basics(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $vehiclesCount       = Vehicle::where('school_id', $schoolId)->count();
        $routesCount         = TransportRoute::where('school_id', $schoolId)->count();
        $stopsCount          = Stop::where('school_id', $schoolId)->count();
        $tripsCount          = VehicleTrip::where('school_id', $schoolId)->count();

        // Only count students who have OPTED for transport (not all with any transport field)
        $mappedStudentsCount = Student::where('school_id', $schoolId)
            ->where('transport_opted', true)
            ->whereNotNull('transport_route')
            ->count();

        $totalExpenses = VehicleExpense::where('school_id', $schoolId)->sum('amount');

        // Recent expense summary (last 5)
        $recentExpenses = VehicleExpense::where('school_id', $schoolId)
            ->with('vehicle')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('school.transport.basics', compact(
            'vehiclesCount',
            'routesCount',
            'stopsCount',
            'tripsCount',
            'mappedStudentsCount',
            'totalExpenses',
            'recentExpenses'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // VEHICLES
    // ─────────────────────────────────────────────────────────────

    public function vehicles(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_no'    => 'required|string|max:50',
                'vehicle_model' => 'nullable|string|max:100',
                'driver_name'   => 'nullable|string|max:100',
                'driver_phone'  => 'nullable|string|max:30',
                'capacity'      => 'required|integer|min:1',
            ]);

            if ($request->filled('id')) {
                $vehicle = Vehicle::where('school_id', $schoolId)->findOrFail($request->id);
                $vehicle->update($request->only(['vehicle_no', 'vehicle_model', 'driver_name', 'driver_phone', 'capacity', 'status']));
                return back()->with('success', 'Vehicle updated successfully!');
            } else {
                Vehicle::create(array_merge(
                    $request->only(['vehicle_no', 'vehicle_model', 'driver_name', 'driver_phone', 'capacity']),
                    ['school_id' => $schoolId, 'status' => true]
                ));
                return back()->with('success', 'Vehicle added successfully!');
            }
        }

        $vehicles = Vehicle::where('school_id', $schoolId)->withCount('trips')->get();
        return view('school.transport.vehicles', compact('vehicles'));
    }

    // ─────────────────────────────────────────────────────────────
    // STOPS
    // ─────────────────────────────────────────────────────────────

    public function stops(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->isMethod('post')) {
            $request->validate([
                'name'      => 'required|string|max:150',
                'landmark'  => 'nullable|string|max:150',
                'pick_fare' => 'required|numeric|min:0',
                'drop_fare' => 'required|numeric|min:0',
            ]);

            // Total fare = pick + drop for legacy compatibility
            $totalFare = (float) $request->pick_fare + (float) $request->drop_fare;

            $data = array_merge(
                $request->only(['name', 'landmark', 'pick_fare', 'drop_fare']),
                ['fare' => $totalFare, 'school_id' => $schoolId]
            );

            if ($request->filled('id')) {
                $stop = Stop::where('school_id', $schoolId)->findOrFail($request->id);
                $stop->update($data);
                return back()->with('success', 'Stop updated successfully!');
            } else {
                Stop::create($data);
                return back()->with('success', 'Stop added successfully!');
            }
        }

        $stops = Stop::where('school_id', $schoolId)->get();
        return view('school.transport.stops', compact('stops'));
    }

    // ─────────────────────────────────────────────────────────────
    // ROUTES
    // ─────────────────────────────────────────────────────────────

    public function routes(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->isMethod('post')) {
            $request->validate([
                'name'        => 'required|string|max:150',
                'description' => 'nullable|string|max:255',
                'pick_fare'   => 'nullable|numeric|min:0',
                'drop_fare'   => 'nullable|numeric|min:0',
            ]);

            $data = array_merge(
                $request->only(['name', 'description', 'pick_fare', 'drop_fare']),
                ['school_id' => $schoolId]
            );

            if ($request->filled('id')) {
                $route = TransportRoute::where('school_id', $schoolId)->findOrFail($request->id);
                $route->update($data);
                return back()->with('success', 'Route updated successfully!');
            } else {
                TransportRoute::create($data);
                return back()->with('success', 'Route created successfully!');
            }
        }

        $routes = TransportRoute::where('school_id', $schoolId)->withCount('students')->get();
        return view('school.transport.routes', compact('routes'));
    }

    // ─────────────────────────────────────────────────────────────
    // TRIP MAPPING
    // ─────────────────────────────────────────────────────────────

    public function tripMapping(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'route_id'   => 'required|exists:transport_routes,id',
                'trip_name'  => 'required|string|max:100',
                'type'       => 'required|in:pickup,drop,both',
                'start_time' => 'nullable|string|max:20',
                'end_time'   => 'nullable|string|max:20',
            ]);

            if ($request->filled('id')) {
                $trip = VehicleTrip::where('school_id', $schoolId)->findOrFail($request->id);
                $trip->update($request->only(['vehicle_id', 'route_id', 'trip_name', 'type', 'start_time', 'end_time']));
                return back()->with('success', 'Trip updated successfully!');
            } else {
                VehicleTrip::create(array_merge(
                    $request->only(['vehicle_id', 'route_id', 'trip_name', 'type', 'start_time', 'end_time']),
                    ['school_id' => $schoolId]
                ));
                return back()->with('success', 'Trip scheduled successfully!');
            }
        }

        $trips    = VehicleTrip::where('school_id', $schoolId)->with(['vehicle', 'route'])->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->where('status', true)->get();
        $routes   = TransportRoute::where('school_id', $schoolId)->get();

        return view('school.transport.trip_mapping', compact('trips', 'vehicles', 'routes'));
    }

    // ─────────────────────────────────────────────────────────────
    // STUDENT ROUTE MAPPING — KEY FIX: transport_opted logic
    // ─────────────────────────────────────────────────────────────

    public function studentMapping(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id'                => 'required|exists:students,id',
                'transport_opted'           => 'nullable|boolean',
                'transport_route'           => 'nullable|string|max:150',
                'transport_route_id'        => 'nullable|exists:transport_routes,id',
                'transport_vehicle_code'    => 'nullable|string|max:100',
                'transport_stop'            => 'nullable|string|max:150',
                'transport_drop_vehicle_code' => 'nullable|string|max:100',
                'transport_pick_fare'       => 'nullable|numeric|min:0',
                'transport_drop_fare'       => 'nullable|numeric|min:0',
                'transport_pickup_location' => 'nullable|string|max:255',
                'transport_drop_location'   => 'nullable|string|max:255',
                'transport_pickup_time'     => 'nullable|string|max:20',
                'transport_drop_time'       => 'nullable|string|max:20',
                'transport_calendar_start'  => 'nullable|date',
                'transport_month'           => 'nullable|string|max:100',
            ]);

            $student = Student::where('school_id', $schoolId)->findOrFail($request->student_id);

            // ── KEY FIX ──────────────────────────────────────────────
            // Transport fee is ONLY applicable when route is explicitly assigned.
            // If route is empty / opted = false → clear transport data and set opted=false.
            $isOpted = !empty($request->transport_route) && !empty($request->transport_route_id);

            if ($isOpted) {
                $student->update([
                    'transport_opted'           => true,
                    'transport_month'           => $request->transport_month,
                    'transport_route'           => $request->transport_route,
                    'transport_route_id'        => $request->transport_route_id,
                    'transport_vehicle_code'    => $request->transport_vehicle_code,
                    'transport_stop'            => $request->transport_stop,
                    'transport_drop_vehicle_code' => $request->transport_drop_vehicle_code,
                    'transport_pick_fare'       => $request->transport_pick_fare ?? 0,
                    'transport_drop_fare'       => $request->transport_drop_fare ?? 0,
                    'transport_pickup_location' => $request->transport_pickup_location,
                    'transport_drop_location'   => $request->transport_drop_location,
                    'transport_pickup_time'     => $request->transport_pickup_time,
                    'transport_drop_time'       => $request->transport_drop_time,
                    'transport_calendar_start'  => $request->transport_calendar_start,
                ]);
            } else {
                // No route assigned → remove transport, zero fares, opted=false
                $student->update([
                    'transport_opted'           => false,
                    'transport_month'           => null,
                    'transport_route'           => null,
                    'transport_route_id'        => null,
                    'transport_vehicle_code'    => null,
                    'transport_stop'            => null,
                    'transport_drop_vehicle_code' => null,
                    'transport_pick_fare'       => null,
                    'transport_drop_fare'       => null,
                    'transport_pickup_location' => null,
                    'transport_drop_location'   => null,
                    'transport_pickup_time'     => null,
                    'transport_drop_time'       => null,
                    'transport_calendar_start'  => null,
                ]);
            }
            
            // Sync student's transport fee billing records immediately
            \App\Models\StudentFee::syncTransportFees($schoolId, $student->id);

            return back()->with('success', 'Student transport assignment updated successfully!');
        }

        $classes  = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $sections = Section::where('school_id', $schoolId)->orderBy('name')->get();

        $query = Student::where('school_id', $schoolId)->with(['class', 'section', 'transportRoute']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('transport_filter')) {
            if ($request->transport_filter === 'opted') {
                $query->where('transport_opted', true)->whereNotNull('transport_route');
            } elseif ($request->transport_filter === 'not_opted') {
                $query->where(function ($q) {
                    $q->where('transport_opted', false)->orWhereNull('transport_route');
                });
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(25)->withQueryString();
        $routes   = TransportRoute::where('school_id', $schoolId)->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->where('status', true)->get();
        $stops    = Stop::where('school_id', $schoolId)->get();

        return view('school.transport.student_mapping', compact(
            'students', 'classes', 'sections', 'routes', 'vehicles', 'stops'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // EXPORT CALENDAR (.ics) for a student's transport schedule
    // ─────────────────────────────────────────────────────────────

    public function exportCalendar(Request $request, $studentId)
    {
        $schoolId = auth()->user()->school_id;
        $student  = Student::where('school_id', $schoolId)->findOrFail($studentId);

        if (!$student->hasTransportAssigned()) {
            return back()->with('error', 'Student does not have transport assigned.');
        }

        $startDate = $student->transport_calendar_start ?? now()->startOfMonth()->toDateString();
        $endDate   = \Carbon\Carbon::parse($startDate)->addMonths(1)->toDateString();

        $pickupTime = $student->transport_pickup_time ?? '07:30';
        $dropTime   = $student->transport_drop_time   ?? '15:00';

        $icsLines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//SchoolCloud ERP//Transport//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        $current = \Carbon\Carbon::parse($startDate);
        $end     = \Carbon\Carbon::parse($endDate);
        $seq     = 1;

        while ($current->lte($end)) {
            // Skip weekends
            if (!$current->isWeekend()) {
                $dtStart = $current->format('Ymd') . 'T' . str_replace(':', '', $pickupTime) . '00';
                $dtEnd   = $current->format('Ymd') . 'T' . str_replace(':', '', $pickupTime) . '3000';
                $uid     = 'transport-pick-' . $student->id . '-' . $current->format('Ymd') . '@schoolcloud';

                $icsLines[] = 'BEGIN:VEVENT';
                $icsLines[] = 'UID:' . $uid;
                $icsLines[] = 'DTSTART:' . $dtStart;
                $icsLines[] = 'DTEND:' . $dtEnd;
                $icsLines[] = 'SUMMARY:🚌 Pickup – ' . $student->full_name;
                $icsLines[] = 'DESCRIPTION:Route: ' . $student->transport_route . '\nStop: ' . ($student->transport_stop ?? 'N/A') . '\nVehicle: ' . ($student->transport_vehicle_code ?? 'N/A');
                $icsLines[] = 'LOCATION:' . ($student->transport_pickup_location ?? $student->transport_stop ?? '');
                $icsLines[] = 'END:VEVENT';

                // Drop event
                $dtDropStart = $current->format('Ymd') . 'T' . str_replace(':', '', $dropTime) . '00';
                $dtDropEnd   = $current->format('Ymd') . 'T' . str_replace(':', '', $dropTime) . '3000';
                $uid2        = 'transport-drop-' . $student->id . '-' . $current->format('Ymd') . '@schoolcloud';

                $icsLines[] = 'BEGIN:VEVENT';
                $icsLines[] = 'UID:' . $uid2;
                $icsLines[] = 'DTSTART:' . $dtDropStart;
                $icsLines[] = 'DTEND:' . $dtDropEnd;
                $icsLines[] = 'SUMMARY:🚌 Drop – ' . $student->full_name;
                $icsLines[] = 'DESCRIPTION:Route: ' . $student->transport_route . '\nVehicle: ' . ($student->transport_drop_vehicle_code ?? $student->transport_vehicle_code ?? 'N/A');
                $icsLines[] = 'LOCATION:' . ($student->transport_drop_location ?? $student->transport_stop ?? '');
                $icsLines[] = 'END:VEVENT';
            }

            $current->addDay();
            $seq++;
        }

        $icsLines[] = 'END:VCALENDAR';
        $icsContent = implode("\r\n", $icsLines);

        $filename = 'transport-' . str_replace(' ', '-', strtolower($student->full_name)) . '.ics';

        return response($icsContent, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // BUS ATTENDANCE
    // ─────────────────────────────────────────────────────────────

    public function busAttendance(Request $request)
    {
        $schoolId        = auth()->user()->school_id;
        $selectedDate    = $request->get('date', now()->toDateString());
        $selectedTripType = $request->get('trip_type', 'pickup');

        if ($request->isMethod('post')) {
            $request->validate(['attendance' => 'nullable|array']);

            DB::transaction(function () use ($schoolId, $selectedDate, $selectedTripType, $request) {
                BusAttendance::where('school_id', $schoolId)
                    ->where('date', $selectedDate)
                    ->where('trip_type', $selectedTripType)
                    ->delete();

                // Only students who have OPTED for transport
                $transportStudentIds = Student::where('school_id', $schoolId)
                    ->where('transport_opted', true)
                    ->whereNotNull('transport_route')
                    ->pluck('id')
                    ->toArray();

                $attData = $request->input('attendance', []);

                foreach ($transportStudentIds as $studId) {
                    $status = isset($attData[$studId]) && $attData[$studId] == 'present' ? 'present' : 'absent';
                    BusAttendance::create([
                        'school_id'  => $schoolId,
                        'student_id' => $studId,
                        'date'       => $selectedDate,
                        'trip_type'  => $selectedTripType,
                        'status'     => $status,
                    ]);
                }
            });

            return back()->with('success', 'Bus attendance saved!');
        }

        $students = Student::where('school_id', $schoolId)
            ->where('transport_opted', true)
            ->whereNotNull('transport_route')
            ->with(['class', 'section'])
            ->get();

        $savedRecords = BusAttendance::where('school_id', $schoolId)
            ->where('date', $selectedDate)
            ->where('trip_type', $selectedTripType)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('school.transport.bus_attendance', compact(
            'students', 'savedRecords', 'selectedDate', 'selectedTripType'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // VEHICLE EXPENSES
    // ─────────────────────────────────────────────────────────────

    public function expenses(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_id'   => 'required|exists:vehicles,id',
                'expense_type' => 'required|string|max:100',
                'amount'       => 'required|numeric|min:0.01',
                'date'         => 'required|date',
                'description'  => 'nullable|string',
                'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
            ]);

            $vehicle = Vehicle::where('school_id', $schoolId)->findOrFail($request->vehicle_id);

            // Get or create default Transport expense head
            $expenseHead = \App\Models\ExpenseHead::firstOrCreate(
                ['school_id' => $schoolId, 'name' => 'Transport'],
                ['created_by' => auth()->id()]
            );

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('transport-expenses', 'public');
            }

            $data = $request->only(['vehicle_id', 'expense_type', 'amount', 'date', 'description']);
            $data['school_id'] = $schoolId;

            if ($request->filled('id')) {
                $expense = VehicleExpense::where('school_id', $schoolId)->findOrFail($request->id);
                if ($attachmentPath) {
                    // Delete old attachment
                    if ($expense->attachment) {
                        Storage::disk('public')->delete($expense->attachment);
                    }
                    $data['attachment'] = $attachmentPath;
                }

                // Sync to SchoolExpense
                $schoolExpense = null;
                if ($expense->school_expense_id) {
                    $schoolExpense = \App\Models\SchoolExpense::where('school_id', $schoolId)->find($expense->school_expense_id);
                }

                $seData = [
                    'school_id'       => $schoolId,
                    'expense_head_id' => $expenseHead->id,
                    'title'           => "Vehicle Expense: " . $request->expense_type . " (" . $vehicle->vehicle_no . ")",
                    'category'        => 'transport',
                    'amount'          => $request->amount,
                    'expense_date'    => $request->date,
                    'description'     => $request->description,
                    'status'          => 'paid',
                ];

                if ($schoolExpense) {
                    $schoolExpense->update($seData);
                } else {
                    $newSe = \App\Models\SchoolExpense::create($seData);
                    $data['school_expense_id'] = $newSe->id;
                }

                $expense->update($data);
                return back()->with('success', 'Expense log updated!');
            } else {
                if ($attachmentPath) {
                    $data['attachment'] = $attachmentPath;
                }

                // Create corresponding SchoolExpense
                $schoolExpense = \App\Models\SchoolExpense::create([
                    'school_id'       => $schoolId,
                    'expense_head_id' => $expenseHead->id,
                    'title'           => "Vehicle Expense: " . $request->expense_type . " (" . $vehicle->vehicle_no . ")",
                    'category'        => 'transport',
                    'amount'          => $request->amount,
                    'expense_date'    => $request->date,
                    'description'     => $request->description,
                    'status'          => 'paid',
                    'created_by'      => auth()->id(),
                ]);

                $data['school_expense_id'] = $schoolExpense->id;

                VehicleExpense::create($data);
                return back()->with('success', 'Expense logged successfully!');
            }
        }

        $expenses = VehicleExpense::where('school_id', $schoolId)
            ->with('vehicle')
            ->orderBy('date', 'desc')
            ->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->get();

        return view('school.transport.expenses', compact('expenses', 'vehicles'));
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────

    public function deleteItem(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $id       = $request->input('id');
        $type     = $request->input('type');

        if ($type === 'vehicle') {
            Vehicle::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'stop') {
            Stop::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'route') {
            TransportRoute::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'trip') {
            VehicleTrip::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'expense') {
            $expense = VehicleExpense::where('school_id', $schoolId)->where('id', $id)->first();
            if ($expense) {
                if ($expense->attachment) {
                    Storage::disk('public')->delete($expense->attachment);
                }
                if ($expense->school_expense_id) {
                    \App\Models\SchoolExpense::where('school_id', $schoolId)->where('id', $expense->school_expense_id)->delete();
                }
                $expense->delete();
            }
        }

        return back()->with('success', 'Item deleted successfully!');
    }
}
