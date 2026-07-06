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

class TransportController extends Controller
{
    private function ensureTransportSeeded($schoolId)
    {
        // No auto-seeding
    }

    public function basics(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        // Fetch counts
        $vehiclesCount = Vehicle::where('school_id', $schoolId)->count();
        $routesCount = TransportRoute::where('school_id', $schoolId)->count();
        $stopsCount = Stop::where('school_id', $schoolId)->count();
        $tripsCount = VehicleTrip::where('school_id', $schoolId)->count();
        $mappedStudentsCount = Student::where('school_id', $schoolId)
            ->where(function($q) {
                $q->whereNotNull('transport_route')
                  ->orWhereNotNull('transport_vehicle_code')
                  ->orWhereNotNull('transport_stop')
                  ->orWhereNotNull('transport_drop_vehicle_code');
            })->count();

        // Expenses summary
        $totalExpenses = VehicleExpense::where('school_id', $schoolId)->sum('amount');

        return view('school.transport.basics', compact(
            'vehiclesCount',
            'routesCount',
            'stopsCount',
            'tripsCount',
            'mappedStudentsCount',
            'totalExpenses'
        ));
    }

    public function vehicles(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_no' => 'required|string|max:50',
                'vehicle_model' => 'nullable|string|max:100',
                'driver_name' => 'nullable|string|max:100',
                'driver_phone' => 'nullable|string|max:30',
                'capacity' => 'required|integer|min:1',
            ]);

            if ($request->filled('id')) {
                // Update
                $vehicle = Vehicle::where('school_id', $schoolId)->findOrFail($request->id);
                $vehicle->update($request->all());
                return back()->with('success', 'Vehicle details updated successfully!');
            } else {
                // Create
                Vehicle::create(array_merge($request->all(), ['school_id' => $schoolId]));
                return back()->with('success', 'New Vehicle added successfully!');
            }
        }

        $vehicles = Vehicle::where('school_id', $schoolId)->get();
        return view('school.transport.vehicles', compact('vehicles'));
    }

    public function stops(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:150',
                'landmark' => 'nullable|string|max:150',
                'fare' => 'required|numeric|min:0',
            ]);

            if ($request->filled('id')) {
                // Update
                $stop = Stop::where('school_id', $schoolId)->findOrFail($request->id);
                $stop->update($request->all());
                return back()->with('success', 'Stop details updated successfully!');
            } else {
                // Create
                Stop::create(array_merge($request->all(), ['school_id' => $schoolId]));
                return back()->with('success', 'New Stop added successfully!');
            }
        }

        $stops = Stop::where('school_id', $schoolId)->get();
        return view('school.transport.stops', compact('stops'));
    }

    public function routes(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:150',
                'description' => 'nullable|string|max:255',
            ]);

            if ($request->filled('id')) {
                // Update
                $route = TransportRoute::where('school_id', $schoolId)->findOrFail($request->id);
                $route->update($request->all());
                return back()->with('success', 'Route details updated successfully!');
            } else {
                // Create
                TransportRoute::create(array_merge($request->all(), ['school_id' => $schoolId]));
                return back()->with('success', 'New Route added successfully!');
            }
        }

        $routes = TransportRoute::where('school_id', $schoolId)->get();
        return view('school.transport.routes', compact('routes'));
    }

    public function tripMapping(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'route_id' => 'required|exists:transport_routes,id',
                'trip_name' => 'required|string|max:100',
                'type' => 'required|in:pickup,drop,both',
                'start_time' => 'nullable|string|max:20',
                'end_time' => 'nullable|string|max:20',
            ]);

            if ($request->filled('id')) {
                // Update
                $trip = VehicleTrip::where('school_id', $schoolId)->findOrFail($request->id);
                $trip->update($request->all());
                return back()->with('success', 'Trip schedule updated successfully!');
            } else {
                // Create
                VehicleTrip::create(array_merge($request->all(), ['school_id' => $schoolId]));
                return back()->with('success', 'New Trip schedule mapped successfully!');
            }
        }

        $trips = VehicleTrip::where('school_id', $schoolId)->with(['vehicle', 'route'])->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->where('status', true)->get();
        $routes = TransportRoute::where('school_id', $schoolId)->get();

        return view('school.transport.trip_mapping', compact('trips', 'vehicles', 'routes'));
    }

    public function studentMapping(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'transport_month' => 'nullable|string|max:100',
                'transport_route' => 'nullable|string|max:150',
                'transport_vehicle_code' => 'nullable|string|max:100',
                'transport_stop' => 'nullable|string|max:150',
                'transport_drop_vehicle_code' => 'nullable|string|max:100',
            ]);

            $student = Student::where('school_id', $schoolId)->findOrFail($request->student_id);
            $student->update([
                'transport_month' => $request->transport_month,
                'transport_route' => $request->transport_route,
                'transport_vehicle_code' => $request->transport_vehicle_code,
                'transport_stop' => $request->transport_stop,
                'transport_drop_vehicle_code' => $request->transport_drop_vehicle_code,
            ]);

            // Sync student's fees because transport route/stop mapping was changed
            FeeManagementController::syncStudentFees($student);

            return back()->with('success', 'Student transport route details updated successfully!');
        }

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $sections = Section::where('school_id', $schoolId)->orderBy('name')->get();

        $query = Student::where('school_id', $schoolId)->with(['class', 'section']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(25)->withQueryString();

        $routes = TransportRoute::where('school_id', $schoolId)->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->where('status', true)->get();
        $stops = Stop::where('school_id', $schoolId)->get();

        return view('school.transport.student_mapping', compact('students', 'classes', 'sections', 'routes', 'vehicles', 'stops'));
    }

    public function busAttendance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        $selectedDate = $request->get('date', now()->toDateString());
        $selectedTripType = $request->get('trip_type', 'pickup');

        if ($request->isMethod('post')) {
            $request->validate([
                'attendance' => 'nullable|array',
            ]);

            DB::transaction(function () use ($schoolId, $selectedDate, $selectedTripType, $request) {
                // Clear existing for this date/trip
                BusAttendance::where('school_id', $schoolId)
                    ->where('date', $selectedDate)
                    ->where('trip_type', $selectedTripType)
                    ->delete();

                // Only students who are active transport users are recorded
                $transportStudentIds = Student::where('school_id', $schoolId)
                    ->where(function($q) {
                        $q->whereNotNull('transport_route')
                          ->orWhereNotNull('transport_vehicle_code')
                          ->orWhereNotNull('transport_stop')
                          ->orWhereNotNull('transport_drop_vehicle_code');
                    })
                    ->pluck('id')
                    ->toArray();

                $attData = $request->input('attendance', []);

                foreach ($transportStudentIds as $studId) {
                    $status = isset($attData[$studId]) && $attData[$studId] == 'present' ? 'present' : 'absent';
                    BusAttendance::create([
                        'school_id' => $schoolId,
                        'student_id' => $studId,
                        'date' => $selectedDate,
                        'trip_type' => $selectedTripType,
                        'status' => $status,
                    ]);
                }
            });

            return back()->with('success', 'Bus attendance saved successfully!');
        }

        // Load students mapped to transport
        $students = Student::where('school_id', $schoolId)
            ->where(function($q) {
                $q->whereNotNull('transport_route')
                  ->orWhereNotNull('transport_vehicle_code')
                  ->orWhereNotNull('transport_stop')
                  ->orWhereNotNull('transport_drop_vehicle_code');
            })
            ->with(['class', 'section'])
            ->get();

        // Get saved records
        $savedRecords = BusAttendance::where('school_id', $schoolId)
            ->where('date', $selectedDate)
            ->where('trip_type', $selectedTripType)
            ->pluck('status', 'student_id')
            ->toArray();

        return view('school.transport.bus_attendance', compact('students', 'savedRecords', 'selectedDate', 'selectedTripType'));
    }

    public function expenses(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTransportSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'expense_type' => 'required|string|max:100',
                'amount' => 'required|numeric|min:0.01',
                'date' => 'required|date',
                'description' => 'nullable|string',
            ]);

            if ($request->filled('id')) {
                // Update
                $expense = VehicleExpense::where('school_id', $schoolId)->findOrFail($request->id);
                $expense->update($request->all());
                return back()->with('success', 'Expense log updated successfully!');
            } else {
                // Create
                VehicleExpense::create(array_merge($request->all(), ['school_id' => $schoolId]));
                return back()->with('success', 'New Expense logged successfully!');
            }
        }

        $expenses = VehicleExpense::where('school_id', $schoolId)->with('vehicle')->orderBy('date', 'desc')->get();
        $vehicles = Vehicle::where('school_id', $schoolId)->get();

        return view('school.transport.expenses', compact('expenses', 'vehicles'));
    }

    public function deleteItem(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $id = $request->input('id');
        $type = $request->input('type');

        if ($type === 'vehicle') {
            Vehicle::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'stop') {
            Stop::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'route') {
            TransportRoute::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'trip') {
            VehicleTrip::where('school_id', $schoolId)->where('id', $id)->delete();
        } elseif ($type === 'expense') {
            VehicleExpense::where('school_id', $schoolId)->where('id', $id)->delete();
        }

        return back()->with('success', 'Item deleted successfully!');
    }
}
