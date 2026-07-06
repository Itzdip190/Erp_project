<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolRequestController extends Controller
{
    public function index()
    {
        $pendingRequests = SchoolRequest::with('plan')->where('status', 'pending')->latest()->get();
        $approvedRequests = SchoolRequest::with('plan')->where('status', 'approved')->latest()->get();
        $rejectedRequests = SchoolRequest::with('plan')->where('status', 'rejected')->latest()->get();

        return view('superadmin.school-requests.index', compact('pendingRequests', 'approvedRequests', 'rejectedRequests'));
    }

    public function approve(SchoolRequest $schoolRequest)
    {
        if ($schoolRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        // Validate uniqueness again to prevent race conditions or DB crashes
        if (School::where('code', strtoupper($schoolRequest->code))->exists()) {
            return redirect()->back()->with('error', "A school with code \"{$schoolRequest->code}\" already exists.");
        }

        if (User::where('email', $schoolRequest->admin_email)->exists()) {
            return redirect()->back()->with('error', "A user with email \"{$schoolRequest->admin_email}\" already exists.");
        }

        try {
            DB::transaction(function () use ($schoolRequest) {
                // 1. Create the school
                $school = School::create([
                    'name'    => $schoolRequest->name,
                    'code'    => strtoupper($schoolRequest->code),
                    'phone'   => $schoolRequest->phone,
                    'address' => $schoolRequest->address,
                    'status'  => 'active',
                ]);

                // 2. Decrypt administrator password
                $decryptedPassword = Crypt::decryptString($schoolRequest->admin_password);

                // 3. Create administrator user for the school
                $user = User::create([
                    'name'      => $schoolRequest->admin_name,
                    'email'     => $schoolRequest->admin_email,
                    'password'  => Hash::make($decryptedPassword),
                    'school_id' => $school->id,
                    'role'      => 'school_admin',
                ]);

                $user->assignRole('school_admin');

                // 4. Create subscription if requested
                if ($schoolRequest->plan_id) {
                    Subscription::create([
                        'school_id' => $school->id,
                        'plan_id'   => $schoolRequest->plan_id,
                        'status'    => 'active',
                        'subscription_ends_at' => now()->addYear(),
                    ]);
                }

                // 5. Update request status
                $schoolRequest->update([
                    'status' => 'approved'
                ]);
            });

            return redirect()->route('superadmin.school-requests.index')
                ->with('success', "School \"{$schoolRequest->name}\" approved and created successfully!");

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

        $schoolRequest->update([
            'status'          => 'rejected',
            'rejected_reason' => $request->input('rejected_reason'),
        ]);

        return redirect()->route('superadmin.school-requests.index')
            ->with('success', "Registration request for \"{$schoolRequest->name}\" rejected.");
    }
}
