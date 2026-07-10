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
            'email'          => 'nullable|email|max:255|unique:users,email',
            'code'           => 'required|string|max:50|unique:schools,code',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'custom_domain'  => 'nullable|string|max:255',
            'status'         => 'required|in:active,inactive,suspended',
            'plan_id'        => 'nullable|exists:plans,id',
            // Admin account
            'admin_name'     => 'required|string|max:100',
            // Session
            'academic_session_name'       => 'nullable|string|max:100',
            'academic_session_start_date' => 'nullable|date',
            'academic_session_end_date'   => 'nullable|date|after_or_equal:academic_session_start_date',
        ]);

        // Admin email = school email; auto-generate password = first 4 letters of school name + @123
        $adminEmail        = $validated['email'] ?? null;
        $generatedPassword = strtolower(substr(preg_replace('/\s+/', '', $validated['name']), 0, 4)) . '@123';

        // Create school
        $school = School::create([
            'name'          => $validated['name'],
            'code'          => strtoupper($validated['code']),
            'phone'         => $validated['phone'] ?? null,
            'address'       => $validated['address'] ?? null,
            'state'         => $validated['state'] ?? 'MH',
            'school_type'   => $validated['school_type'] ?? 'CBSE',
            'director_name' => $validated['director_name'] ?? $validated['admin_name'],
            'email'         => $adminEmail,
            'custom_domain' => $validated['custom_domain'] ?? null,
            'status'        => $validated['status'],
        ]);

        // Create admin user for the school — email = school email, password auto-generated
        $user = User::create([
            'name'      => $validated['admin_name'],
            'email'     => $adminEmail,
            'password'  => Hash::make($generatedPassword),
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
        $sessionName  = $validated['academic_session_name']       ?? (date('Y') . '-' . (date('Y') + 1));
        $sessionStart = $validated['academic_session_start_date'] ?? date('Y-04-01');
        $sessionEnd   = $validated['academic_session_end_date']   ?? date('Y-03-31', strtotime('+1 year'));

        \App\Models\AcademicSession::create([
            'school_id'  => $school->id,
            'name'       => $sessionName,
            'start_date' => $sessionStart,
            'end_date'   => $sessionEnd,
            'is_current' => true,
        ]);

        return redirect()->route('superadmin.schools.index')
            ->with('success', "School \"{$school->name}\" created! Admin login: {$adminEmail} / Password: {$generatedPassword}");
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

        $students = \App\Models\Student::where('school_id', $school->id)
            ->whereIn('id', $studentIds)
            ->get();

        foreach ($students as $student) {
            // Delete associated student user record
            if ($student->user_id) {
                \App\Models\User::where('id', $student->user_id)->delete();
            }

            // Check if guardian email is used by other students in the same school (excluding current batch)
            if ($student->guardian_email) {
                $otherStudentsExist = \App\Models\Student::where('school_id', $school->id)
                    ->where('guardian_email', $student->guardian_email)
                    ->whereNotIn('id', $studentIds)
                    ->exists();

                if (!$otherStudentsExist) {
                    // Delete parent user record
                    \App\Models\User::where('school_id', $school->id)
                        ->where('email', $student->guardian_email)
                        ->delete();
                }
            }

            // Force delete the student record
            $student->forceDelete();
        }

        \Illuminate\Support\Facades\Cache::forget('students_list_version_' . $school->id);
        \Illuminate\Support\Facades\Cache::put('students_list_version_' . $school->id, time(), 86400);

        return redirect()->back()->with('success', 'Selected student(s) permanently deleted.');
    }

    // ─── Reset School Data (preserve students, teachers, classes) ─
    public function resetDataPage(School $school): \Illuminate\Contracts\View\View
    {
        return view('superadmin.schools.reset_data', compact('school'));
    }

    public function resetData(Request $request, School $school): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'confirm_name' => ['required', 'string', function ($attribute, $value, $fail) use ($school) {
                if (strtolower(trim($value)) !== strtolower(trim($school->name))) {
                    $fail('School name does not match. Please type the exact school name to confirm.');
                }
            }],
        ]);

        $sid = $school->id;

        \Illuminate\Support\Facades\DB::transaction(function () use ($sid) {

            // ── Fee module ────────────────────────────────────────────
            \Illuminate\Support\Facades\DB::table('fee_invoices')      ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_receipts')      ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_refunds')       ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('pending_cheques')   ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('payment_links')     ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('student_fees')      ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('class_wise_fees')   ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_components')    ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_schedules')     ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_discounts')     ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_fines')         ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('misc_fees')         ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('optional_fee_mappings')->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('fee_configurations')->where('school_id', $sid)->delete();

            // Reset fee_schedule_id on students so they get re-assigned cleanly
            \Illuminate\Support\Facades\DB::table('students')
                ->where('school_id', $sid)
                ->update(['fee_schedule_id' => null, 'fee_visible' => 1]);

            // ── Attendance ────────────────────────────────────────────
            \Illuminate\Support\Facades\DB::table('student_attendances')->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('staff_attendances')  ->where('school_id', $sid)->delete();

            // ── Timetables ────────────────────────────────────────────
            \Illuminate\Support\Facades\DB::table('timetable_substitutions')->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('timetables')            ->where('school_id', $sid)->delete();
            if (\Illuminate\Support\Facades\Schema::hasTable('timetable_group_periods')) {
                $groupIds = \Illuminate\Support\Facades\DB::table('timetable_groups')->where('school_id', $sid)->pluck('id');
                \Illuminate\Support\Facades\DB::table('timetable_group_periods')->whereIn('timetable_group_id', $groupIds)->delete();
            }
            \Illuminate\Support\Facades\DB::table('timetable_groups')      ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('class_timetable_cells') ->where('school_id', $sid)->delete();

            // ── Exams & Marks ─────────────────────────────────────────
            \Illuminate\Support\Facades\DB::table('student_marks')   ->where('school_id', $sid)->delete();
            $examIds = \Illuminate\Support\Facades\DB::table('exams')->where('school_id', $sid)->pluck('id');
            \Illuminate\Support\Facades\DB::table('exam_subjects')   ->whereIn('exam_id', $examIds)->delete();
            \Illuminate\Support\Facades\DB::table('exams')           ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('offline_tests')   ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('grade_scales')    ->where('school_id', $sid)->delete();

            // ── Expenses & Income ─────────────────────────────────────
            \Illuminate\Support\Facades\DB::table('school_expenses')  ->where('school_id', $sid)->delete();
            if (\Illuminate\Support\Facades\Schema::hasTable('voucher_payments')) {
                $voucherIds = \Illuminate\Support\Facades\DB::table('expense_vouchers')->where('school_id', $sid)->pluck('id');
                \Illuminate\Support\Facades\DB::table('voucher_payments')->whereIn('expense_voucher_id', $voucherIds)->delete();
            }
            \Illuminate\Support\Facades\DB::table('expense_vouchers') ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('expense_heads')    ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('account_transfers')->where('school_id', $sid)->delete();

            \Illuminate\Support\Facades\DB::table('school_incomes')   ->where('school_id', $sid)->delete();
            if (\Illuminate\Support\Facades\Schema::hasTable('voucher_receipts')) {
                $incomeVoucherIds = \Illuminate\Support\Facades\DB::table('income_vouchers')->where('school_id', $sid)->pluck('id');
                \Illuminate\Support\Facades\DB::table('voucher_receipts')->whereIn('income_voucher_id', $incomeVoucherIds)->delete();
            }
            \Illuminate\Support\Facades\DB::table('income_vouchers')  ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('income_heads')     ->where('school_id', $sid)->delete();

            // ── Gallery, Notices, Events ──────────────────────────────
            \Illuminate\Support\Facades\DB::table('gallery_posts') ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('events')        ->where('school_id', $sid)->delete();

            // ── Teacher Assignments & Study Materials ─────────────────
            if (\Illuminate\Support\Facades\Schema::hasTable('teacher_assignment_submissions')) {
                $assignmentIds = \Illuminate\Support\Facades\DB::table('teacher_assignments')->where('school_id', $sid)->pluck('id');
                \Illuminate\Support\Facades\DB::table('teacher_assignment_submissions')->whereIn('assignment_id', $assignmentIds)->delete();
            }
            \Illuminate\Support\Facades\DB::table('teacher_assignments')->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('study_materials')   ->where('school_id', $sid)->delete();

            // ── Academic Sessions (keeps classes/sections intact) ─────
            \Illuminate\Support\Facades\DB::table('student_sessions')  ->where('school_id', $sid)->delete();
            \Illuminate\Support\Facades\DB::table('academic_sessions') ->where('school_id', $sid)->delete();

            // ── Import Logs ───────────────────────────────────────────
            \Illuminate\Support\Facades\DB::table('import_logs') ->where('school_id', $sid)->delete();

            // ── School Banks ──────────────────────────────────────────
            if (\Illuminate\Support\Facades\Schema::hasTable('school_banks')) {
                \Illuminate\Support\Facades\DB::table('school_banks')->where('school_id', $sid)->delete();
            }
        });

        // Clear any cached data for this school
        \Illuminate\Support\Facades\Cache::forget('students_list_version_' . $sid);

        return redirect()
            ->route('superadmin.schools.edit', $school)
            ->with('success', "School \"{$school->name}\" data has been reset successfully. Students, teachers, and classes are preserved.");
    }
}

