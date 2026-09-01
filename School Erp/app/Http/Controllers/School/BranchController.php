<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class BranchController extends Controller
{
    /**
     * Switch the active school context to an authorized sister branch.
     */
    public function switchBranch(Request $request)
    {
        $validated = $request->validate([
            'school_id' => 'required|integer|exists:schools,id',
        ]);

        $targetSchoolId = (int)$validated['school_id'];
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $targetSchool = School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->findOrFail($targetSchoolId);

        // If user is already on this school, simply redirect
        if ((int)$user->school_id === $targetSchoolId) {
            return redirect()->route('school.dashboard')->with('info', "Already viewing {$targetSchool->name}.");
        }

        // Allow superadmin (including active impersonation) to switch to any active school
        $isSuperAdmin = $user->hasRole('superadmin') || session('is_impersonating');

        if (!$isSuperAdmin) {
            $currentSchool = School::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($user->school_id);

            if (!$currentSchool) {
                abort(403, 'Current school context not found.');
            }

            // Security Validation: Both schools must belong to the same active branch group with branch access enabled
            $isAuthorizedBranch = $currentSchool->branch_access_enabled
                && $targetSchool->branch_access_enabled
                && $currentSchool->branch_group_id
                && $targetSchool->branch_group_id
                && ((int)$currentSchool->branch_group_id === (int)$targetSchool->branch_group_id)
                && $targetSchool->status === 'active';

            if (!$isAuthorizedBranch) {
                abort(403, 'Unauthorized branch switch attempt. This school is not an assigned branch of your group.');
            }
        }

        // Check if target school is active
        if ($targetSchool->status !== 'active') {
            return redirect()->back()->with('error', "Cannot switch to {$targetSchool->name} because it is inactive or suspended.");
        }

        // Check if there is a matching user account in the target school with the same email
        $matchingUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
            ->where('school_id', $targetSchool->id)
            ->where('email', $user->email)
            ->where('is_active', true)
            ->first();

        if ($matchingUser && $matchingUser->id !== $user->id) {
            Auth::login($matchingUser);
            $activeUser = $matchingUser;
        } else {
            // Update the user's active school context
            $user->update(['school_id' => $targetSchool->id]);
            $activeUser = $user;
        }

        // Update session context
        session([
            'school_id'   => $targetSchool->id,
            'school_code' => $targetSchool->code,
        ]);

        // Bind new school instance in container
        app()->instance('currentSchool', $targetSchool);
        $request->attributes->set('school', $targetSchool);

        // Reset permission cache
        try {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Throwable $e) {
            // ignore if registrar not bound
        }

        // Reset student/parent active student session key if changing school
        session()->forget('active_student_id');

        // Redirect based on role
        if ($activeUser->hasRole('school_admin') || $activeUser->hasRole('superadmin') || in_array($activeUser->role, ['school_admin', 'superadmin', 'admin'])) {
            return redirect()->route('school.dashboard')->with('success', "Switched to {$targetSchool->name} successfully.");
        } elseif ($activeUser->hasRole('teacher') || $activeUser->hasRole('staff') || $activeUser->role === 'teacher') {
            return redirect()->route('teacher.dashboard')->with('success', "Switched to {$targetSchool->name} successfully.");
        }

        return redirect()->route('school.dashboard')->with('success', "Switched to {$targetSchool->name} successfully.");
    }
}
