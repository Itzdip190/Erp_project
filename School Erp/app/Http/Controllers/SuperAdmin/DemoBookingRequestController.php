<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DemoBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DemoBookingRequestController extends Controller
{
    /**
     * Auto-ensure schema up to date.
     */
    private function ensureColumnsExist(): void
    {
        try {
            if (!Schema::hasTable('demo_bookings')) {
                Schema::create('demo_bookings', function ($table) {
                    $table->id();
                    $table->string('full_name');
                    $table->string('email');
                    $table->string('phone');
                    $table->string('institute_name')->nullable();
                    $table->string('student_count')->nullable();
                    $table->string('role')->nullable();
                    $table->string('city')->nullable();
                    $table->string('state')->nullable();
                    $table->string('country')->nullable();
                    $table->string('booking_date')->nullable();
                    $table->string('booking_time')->nullable();
                    $table->string('timezone')->nullable();
                    $table->string('source')->default('Website');
                    $table->text('message')->nullable();
                    $table->string('status')->default('pending');
                    $table->timestamps();
                });
            } else {
                Schema::table('demo_bookings', function ($table) {
                    if (!Schema::hasColumn('demo_bookings', 'booking_date')) {
                        $table->string('booking_date')->nullable();
                    }
                    if (!Schema::hasColumn('demo_bookings', 'booking_time')) {
                        $table->string('booking_time')->nullable();
                    }
                    if (!Schema::hasColumn('demo_bookings', 'timezone')) {
                        $table->string('timezone')->nullable();
                    }
                    if (!Schema::hasColumn('demo_bookings', 'source')) {
                        $table->string('source')->default('Website');
                    }
                });
            }
        } catch (\Throwable $e) {
            // log warning
        }
    }

    /**
     * Display listing of Demo Booking Requests with Search, Filter & Pagination.
     */
    public function index(Request $request)
    {
        $this->ensureColumnsExist();

        $query = DemoBooking::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('institute_name', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && in_array($request->status, ['pending', 'contacted', 'completed'])) {
            $query->where('status', $request->status);
        }

        // Metrics Counts
        $totalCount     = DemoBooking::count();
        $pendingCount   = DemoBooking::where('status', 'pending')->count();
        $contactedCount = DemoBooking::where('status', 'contacted')->count();
        $completedCount = DemoBooking::where('status', 'completed')->count();

        $demoBookings = $query->latest()->paginate(15)->appends($request->all());

        return view('superadmin.demo-requests.index', compact(
            'demoBookings',
            'totalCount',
            'pendingCount',
            'contactedCount',
            'completedCount'
        ));
    }

    /**
     * Display details of a specific Demo Booking Request.
     */
    public function show(DemoBooking $demoBooking)
    {
        $this->ensureColumnsExist();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $demoBooking
            ]);
        }

        return view('superadmin.demo-requests.show', compact('demoBooking'));
    }

    /**
     * Update Booking Status (pending, contacted, completed).
     */
    public function updateStatus(Request $request, DemoBooking $demoBooking)
    {
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'status' => 'required|string|in:pending,contacted,completed',
        ]);

        $demoBooking->update([
            'status' => $validated['status'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Demo request status updated to '{$validated['status']}'.",
                'booking' => $demoBooking,
            ]);
        }

        return redirect()->back()->with('success', "Demo request status updated to '{$validated['status']}' successfully.");
    }

    /**
     * Delete a Demo Booking Request.
     */
    public function destroy(DemoBooking $demoBooking)
    {
        $this->ensureColumnsExist();

        $demoBooking->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Demo request deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Demo request deleted successfully.');
    }
}
