<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\SchoolRequestApproved;
use App\Mail\SchoolRequestRejected;
use App\Models\School;
use App\Models\SchoolRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SchoolRequestController extends Controller
{
    public function index()
    {
        $pendingRequests  = SchoolRequest::with('plan')->where('status', 'pending')->latest()->get();
        $approvedRequests = SchoolRequest::with('plan')->where('status', 'approved')->latest()->get();
        $rejectedRequests = SchoolRequest::with('plan')->where('status', 'rejected')->latest()->get();

        return view('superadmin.school-requests.index', compact('pendingRequests', 'approvedRequests', 'rejectedRequests'));
    }

    public function approve(SchoolRequest $schoolRequest)
    {
        if ($schoolRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        // Re-generate a proper school code based on state (or fallback)
        $state           = $schoolRequest->state ?? 'MH';
        $generatedCode   = School::generateNextCode($state);

        if (School::where('code', strtoupper($generatedCode))->exists()) {
            return redirect()->back()->with('error', "A school with code \"{$generatedCode}\" already exists.");
        }

        if (User::where('email', $schoolRequest->email)->exists()) {
            return redirect()->back()->with('error', "A user with email \"{$schoolRequest->email}\" already exists.");
        }

        // Auto-generate password: first 4 letters of school name (lowercase) + @123
        $generatedPassword = strtolower(substr(preg_replace('/\s+/', '', $schoolRequest->name), 0, 4)) . '@123';

        try {
            DB::transaction(function () use ($schoolRequest, $generatedCode, $generatedPassword) {
                // 1. Create the school
                $school = School::create([
                    'name'          => $schoolRequest->name,
                    'code'          => strtoupper($generatedCode),
                    'phone'         => $schoolRequest->phone,
                    'address'       => $schoolRequest->address,
                    'state'         => $schoolRequest->state ?? 'MH',
                    'school_type'   => $schoolRequest->school_type,
                    'director_name' => $schoolRequest->director_name ?? $schoolRequest->name,
                    'email'         => $schoolRequest->email,
                    'status'        => 'active',
                ]);

                // 2. Create administrator user — email = school email
                $user = User::create([
                    'name'      => $schoolRequest->admin_name ?: $schoolRequest->name,
                    'email'     => $schoolRequest->email,
                    'password'  => Hash::make($generatedPassword),
                    'school_id' => $school->id,
                    'role'      => 'school_admin',
                ]);

                $user->assignRole('school_admin');

                // 3. Create subscription if a plan was requested
                if ($schoolRequest->plan_id) {
                    Subscription::create([
                        'school_id'            => $school->id,
                        'plan_id'              => $schoolRequest->plan_id,
                        'status'               => 'active',
                        'subscription_ends_at' => now()->addYear(),
                    ]);
                }

                // 4. Create academic session if provided, otherwise create a default one
                $sessionName  = $schoolRequest->academic_session_name  ?? (date('Y') . '-' . (date('Y') + 1));
                $sessionStart = $schoolRequest->academic_session_start_date ?? date('Y-04-01');
                $sessionEnd   = $schoolRequest->academic_session_end_date   ?? date('Y-03-31', strtotime('+1 year'));

                \App\Models\AcademicSession::create([
                    'school_id'  => $school->id,
                    'name'       => $sessionName,
                    'start_date' => $sessionStart,
                    'end_date'   => $sessionEnd,
                    'is_current' => true,
                ]);

                // 5. Update request status
                $schoolRequest->update(['status' => 'approved']);
            });

            // Send approval email with credentials
            try {
                Mail::to($schoolRequest->email)->send(new SchoolRequestApproved($schoolRequest, $generatedPassword));
            } catch (\Exception $e) {
                Log::warning('SchoolRequest approval email failed: ' . $e->getMessage());
            }

            return redirect()->route('superadmin.school-requests.index')
                ->with('success', "School \"{$schoolRequest->name}\" approved! Login credentials sent to {$schoolRequest->email}.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve school request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SchoolRequest $schoolRequest)
    {
        if ($schoolRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be rejected.');
        }

        $request->validate([
            'rejected_reason' => 'nullable|string|max:1000',
        ]);

        $reason = $request->input('rejected_reason');

        $schoolRequest->update([
            'status'          => 'rejected',
            'rejected_reason' => $reason,
        ]);

        // Send rejection email
        try {
            Mail::to($schoolRequest->email)->send(new SchoolRequestRejected($schoolRequest, $reason));
        } catch (\Exception $e) {
            Log::warning('SchoolRequest rejection email failed: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.school-requests.index')
            ->with('success', "Registration request for \"{$schoolRequest->name}\" rejected and school notified.");
    }
}
