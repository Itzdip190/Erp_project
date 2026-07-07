<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class SchoolController extends Controller
{
    // ─── List All Schools ──────────────────────────────────────
    public function index(): View
    {
        $schools = School::with(['users.roles', 'subscriptions' => function($q) {
            $q->latest();
        }, 'subscriptions.plan'])
        ->withCount([
            'students as active_students_count' => function ($query) {
                $query->where('is_active', 1);
            },
            'students as inactive_students_count' => function ($query) {
                $query->where('is_active', 0);
            },
            'staff as staff_count'
        ])
        ->get();

        return view('superadmin.schools.index', compact('schools'));
    }

    // ─── Show Create School Form ───────────────────────────────
    public function create(): View
    {
        $plans = \App\Models\Plan::all();
        $states = \App\Models\School::getStatesList();
        return view('superadmin.schools.create', compact('plans', 'states'));
    }

    // ─── Store New School ──────────────────────────────────────
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'state'          => 'nullable|string|size:2',
            'school_type'    => 'nullable|string|in:CBSE,CBSE PATTERN,ICSE,STATE BOARD',
            'director_name'  => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'code'           => 'required|string|max:50|unique:schools,code',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'custom_domain'  => 'nullable|string|max:255',
            'status'         => 'required|in:active,inactive,suspended',
            'plan_id'        => 'nullable|exists:plans,id',
            // Admin account
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            // Session
            'academic_session_name'       => 'nullable|string|max:100',
            'academic_session_start_date' => 'nullable|date',
            'academic_session_end_date'   => 'nullable|date|after_or_equal:academic_session_start_date',
        ]);

        // Create school
        $school = School::create([
            'name'          => $validated['name'],
            'code'          => strtoupper($validated['code']),
            'phone'         => $validated['phone'] ?? null,
            'address'       => $validated['address'] ?? null,
            'state'         => $validated['state'] ?? 'MH',
            'school_type'   => $validated['school_type'] ?? 'CBSE',
            'director_name' => $validated['director_name'] ?? $validated['admin_name'],
            'email'         => $validated['email'] ?? $validated['admin_email'],
            'custom_domain' => $validated['custom_domain'] ?? null,
            'status'        => $validated['status'],
        ]);

        // Create admin user for the school
        $user = User::create([
            'name'      => $validated['admin_name'],
            'email'     => $validated['admin_email'],
            'password'  => Hash::make($validated['admin_password']),
            'school_id' => $school->id,
            'role'      => 'school_admin',
        ]);

        $user->assignRole('school_admin');

        // Create subscription if plan is selected
        if (!empty($validated['plan_id'])) {
            \App\Models\Subscription::create([
                'school_id' => $school->id,
                'plan_id'   => $validated['plan_id'],
                'status'    => 'active',
                'subscription_ends_at' => now()->addYear(),
            ]);
        }

        // Create academic session
        $sessionName = $validated['academic_session_name'] ?? (date('Y') . '-' . (date('Y') + 1));
        $sessionStart = $validated['academic_session_start_date'] ?? date('Y-04-01');
        $sessionEnd = $validated['academic_session_end_date'] ?? date('Y-03-31', strtotime('+1 year'));

        \App\Models\AcademicSession::create([
            'school_id'  => $school->id,
            'name'       => $sessionName,
            'start_date' => $sessionStart,
            'end_date'   => $sessionEnd,
            'is_current' => true,
        ]);

        return redirect()->route('superadmin.schools.index')
            ->with('success', "School \"{$school->name}\" created successfully! Admin login: {$validated['admin_email']}");
    }

    // ─── Direct Login / Impersonate School Admin ────────────────
    public function impersonate(School $school): \Illuminate\Http\RedirectResponse
    {
        // Find the admin user for this school
        $admin = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'school_admin');
            })
            ->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'No school administrator account found for this school.');
        }

        $superadminId = Auth::id();

        // Set session variables for the school environment resolution
        session([
            'school_code' => $school->code,
            'is_impersonating' => true,
            'original_user_id' => $superadminId,
        ]);

        // Login as the school admin
        Auth::login($admin);

        return redirect()->route('school.dashboard')->with('success', "Logged in directly to {$school->name}'s dashboard.");
    }

    public function exitImpersonate(): \Illuminate\Http\RedirectResponse
    {
        if (session()->has('is_impersonating') && session()->has('original_user_id')) {
            $originalUserId = session('original_user_id');
            $originalUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($originalUserId);
            
            if ($originalUser && $originalUser->hasRole('superadmin')) {
                // Clear session keys
                session()->forget(['is_impersonating', 'original_user_id', 'school_code']);

                // Login as superadmin
                Auth::login($originalUser);

                return redirect()->route('superadmin.dashboard')->with('success', 'Returned to Super Admin dashboard.');
            }
        }

        return redirect()->route('login');
    }

    // ─── Edit School Form ──────────────────────────────────────
    public function edit(School $school): View
    {
        $admin = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'school_admin');
            })
            ->first();

        $plans = \App\Models\Plan::all();
        $states = \App\Models\School::getStatesList();
        $currentSub = $school->subscriptions()->latest()->first();

        return view('superadmin.schools.edit', compact('school', 'admin', 'plans', 'currentSub', 'states'));
    }

    // ─── Update School Details ─────────────────────────────────
    public function update(Request $request, School $school): \Illuminate\Http\RedirectResponse
    {
        $admin = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'school_admin');
            })
            ->first();

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'state'          => 'nullable|string|size:2',
            'school_type'    => 'nullable|string|in:CBSE,CBSE PATTERN,ICSE,STATE BOARD',
            'director_name'  => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'code'           => 'required|string|max:50|unique:schools,code,' . $school->id,
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'custom_domain'  => 'nullable|string|max:255',
            'status'         => 'required|in:active,inactive,suspended',
            'plan_id'        => 'nullable|exists:plans,id',
            // Admin account details and optional password reset
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => 'required|email|unique:users,email,' . ($admin ? $admin->id : 0),
            'admin_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update school
        $school->update([
            'name'          => $validated['name'],
            'code'          => strtoupper($validated['code']),
            'phone'         => $validated['phone'] ?? null,
            'address'       => $validated['address'] ?? null,
            'state'         => $validated['state'] ?? ($school->state ?? 'MH'),
            'school_type'   => $validated['school_type'] ?? ($school->school_type ?? 'CBSE'),
            'director_name' => $validated['director_name'] ?? ($school->director_name ?? $validated['admin_name']),
            'email'         => $validated['email'] ?? ($school->email ?? $validated['admin_email']),
            'custom_domain' => $validated['custom_domain'] ?? null,
            'status'        => $validated['status'],
        ]);

        // Update or create admin account
        if ($admin) {
            $adminData = [
                'name'  => $validated['admin_name'],
                'email' => $validated['admin_email'],
            ];

            if (!empty($validated['admin_password'])) {
                $adminData['password'] = Hash::make($validated['admin_password']);
            }

            $admin->update($adminData);
        } else {
            // Fallback: create admin user if it didn't exist
            $admin = User::create([
                'name'      => $validated['admin_name'],
                'email'     => $validated['admin_email'],
                'password'  => Hash::make($validated['admin_password'] ?? 'Welcome@2026!'),
                'school_id' => $school->id,
                'role'      => 'school_admin',
            ]);
            $admin->assignRole('school_admin');
        }

        // Update or create subscription
        $sub = $school->subscriptions()->latest()->first();
        if (!empty($validated['plan_id'])) {
            if ($sub) {
                $sub->update([
                    'plan_id' => $validated['plan_id'],
                    'status' => 'active',
                    'subscription_ends_at' => now()->addYear(),
                ]);
            } else {
                \App\Models\Subscription::create([
                    'school_id' => $school->id,
                    'plan_id'   => $validated['plan_id'],
                    'status'    => 'active',
                    'subscription_ends_at' => now()->addYear(),
                ]);
            }
        } else {
            if ($sub) {
                $sub->delete();
            }
        }

        return redirect()->route('superadmin.schools.index')
            ->with('success', "School \"{$school->name}\" updated successfully!");
    }

    // ─── Delete School & Associated Users ──────────────────────
    public function destroy(School $school): \Illuminate\Http\RedirectResponse
    {
        // Delete all users belonging to this school using Eloquent models so delete events run
        $school->users->each(function($user) {
            $user->delete();
        });

        // Delete the school
        $school->delete();

        return redirect()->route('superadmin.schools.index')
            ->with('success', "School \"{$school->name}\" and all associated data deleted successfully!");
    }

    // ─── Toggle School Status ──────────────────────────────────
    public function toggleStatus(School $school): \Illuminate\Http\RedirectResponse
    {
        $school->status = $school->status === 'active' ? 'suspended' : 'active';
        $school->save();

        return redirect()->back()->with('success', "School \"{$school->name}\" status updated to \"{$school->status}\" successfully!");
    }

    // ─── Inactive Student Management ────────────────────────────
    public function inactiveStudents(School $school): View
    {
        $inactiveStudents = \App\Models\Student::where('school_id', $school->id)
            ->where('is_active', 0)
            ->paginate(50);

        return view('superadmin.schools.inactive-students', compact('school', 'inactiveStudents'));
    }

    public function restoreStudents(Request $request, School $school)
    {
        $studentIds = $request->input('student_ids', []);
        
        if (empty($studentIds)) {
            return redirect()->back()->with('error', 'No students selected.');
        }

        \App\Models\Student::where('school_id', $school->id)
            ->whereIn('id', $studentIds)
            ->update(['is_active' => 1]);

        \Illuminate\Support\Facades\Cache::forget('students_list_version_' . $school->id);
        \Illuminate\Support\Facades\Cache::put('students_list_version_' . $school->id, time(), 86400);

        return redirect()->back()->with('success', 'Selected student(s) restored successfully.');
    }

    public function deleteStudentsPermanently(Request $request, School $school)
    {
        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return redirect()->back()->with('error', 'No students selected.');
        }

        \App\Models\Student::where('school_id', $school->id)
            ->whereIn('id', $studentIds)
            ->forceDelete();

        \Illuminate\Support\Facades\Cache::forget('students_list_version_' . $school->id);
        \Illuminate\Support\Facades\Cache::put('students_list_version_' . $school->id, time(), 86400);

        return redirect()->back()->with('success', 'Selected student(s) permanently deleted.');
    }
}
