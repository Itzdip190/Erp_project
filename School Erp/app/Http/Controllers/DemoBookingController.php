<?php

namespace App\Http\Controllers;

use App\Mail\DemoBookingClientMail;
use App\Mail\DemoBookingSuperAdminMail;
use App\Models\DemoBooking;
use App\Http\Controllers\SuperAdmin\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DemoBookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:30',
            'institute_name' => 'nullable|string|max:255',
            'student_count'  => 'nullable|string|max:100',
            'role'           => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'country'        => 'required|string|max:100',
            'message'        => 'required|string|max:2000',
        ]);

        $booking = DemoBooking::create([
            'full_name'      => $validated['full_name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'institute_name' => $validated['institute_name'] ?? null,
            'student_count'  => $validated['student_count'] ?? null,
            'role'           => $validated['role'],
            'city'           => $validated['city'],
            'state'          => $validated['state'],
            'country'        => $validated['country'],
            'message'        => $validated['message'],
            'status'         => 'pending',
        ]);

        $superAdminEmail = SettingsController::getSuperAdminEmail();

        // Send Email to Superadmin
        try {
            Mail::to($superAdminEmail)->send(new DemoBookingSuperAdminMail($booking));
        } catch (\Exception $e) {
            Log::warning('Demo booking superadmin notification email failed: ' . $e->getMessage());
        }

        // Send Confirmation Email to Client
        try {
            Mail::to($booking->email)->send(new DemoBookingClientMail($booking));
        } catch (\Exception $e) {
            Log::warning('Demo booking client confirmation email failed: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your demo request has been submitted. Our product team will get in touch with you shortly.',
            ]);
        }

        return redirect()->back()->with('success', 'Thank you! Your demo request has been submitted. Our product team will get in touch with you shortly.');
    }
}
