<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ModulePermission;
use App\Models\StaffModuleAccess;
use App\Models\User;
use App\Support\ModuleRegistry;
use App\Models\Designation;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /* ─────────────────────────────────────────────────────────────────
     *  ROLE CATEGORY – staff multiple designations mapping
     * ───────────────────────────────────────────────────────────────── */

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $search   = $request->get('search');

        $query = User::where('school_id', $schoolId)
            ->whereHas('staff')
            ->with(['staff.designations', 'roles']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('employee_id', 'like', "%{$search}%");
                  });
            });
        }

        $staffList = $query->orderBy('name')->paginate(10);
        $designations = Designation::where('school_id', $schoolId)->get();

        return view('school.roles.index', compact('staffList', 'designations', 'search'));
    }

    public function updateStaffDesignations(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'designation_ids'  => 'array',
            'designation_ids.*'=> 'integer|exists:designations,id',
        ]);

        $schoolId = auth()->user()->school_id;
        $user = User::where('school_id', $schoolId)->findOrFail($request->user_id);
        $staff = $user->staff;

        if (!$staff) {
            return response()->json(['success' => false, 'error' => 'Staff profile not found.'], 404);
        }

        $designationIds = $request->input('designation_ids', []);

        // Sync designations in pivot table
        $staff->designations()->sync($designationIds);

        // Update the primary designation_id in the staff table (for backward compatibility)
        $primaryDesignationId = !empty($designationIds) ? $designationIds[0] : null;
        $staff->update(['designation_id' => $primaryDesignationId]);

        // Sync Spatie roles based on designated roles
        $roles = [];
        $allDesignations = Designation::whereIn('id', $designationIds)->get();
        foreach ($allDesignations as $desg) {
            $name = strtolower($desg->name);
            if (str_contains($name, 'admin') || str_contains($name, 'principal')) {
                $roles[] = 'school_admin';
            } elseif (str_contains($name, 'accountant')) {
                $roles[] = 'accountant';
            } elseif (str_contains($name, 'driver')) {
                $roles[] = 'driver';
            } else {
                $roles[] = 'teacher';
            }
        }
        $roles = array_unique($roles);
        if (empty($roles)) {
            $roles = ['teacher']; // default fallback
        }
        $user->syncRoles($roles);

        return response()->json([
            'success' => true,
            'message' => 'Designations and Spatie roles updated successfully!',
            'designations' => $allDesignations->map(fn($d) => ['id' => $d->id, 'name' => $d->name]),
            'roles' => $roles
        ]);
    }

    /* ─────────────────────────────────────────────────────────────────
     *  STAFF ACCESS CONTROL – per-staff, per-module, per-feature access
     * ───────────────────────────────────────────────────────────────── */

    public function staffAccess()
    {
        $schoolId = auth()->user()->school_id;
        $modules  = ModuleRegistry::all();

        // All teaching/admin staff for this school
        $staff = User::where('school_id', $schoolId)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['teacher', 'accountant', 'school_admin']))
            ->with('roles')
            ->orderBy('name')
            ->get();

        // Defensive: guard against missing table (first deployment before migration)
        $access = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('staff_module_access')) {
            $access = StaffModuleAccess::where('school_id', $schoolId)
                ->get()
                ->keyBy(fn ($r) => "{$r->user_id}.{$r->module_key}.{$r->feature_key}");
        }

        return view('school.roles.staff_access', compact('modules', 'staff', 'access'));
    }

    /**
     * AJAX: toggle a single staff-module-feature row.
     */
    public function updateStaffAccess(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'module_key'   => 'required|string',
            'feature_key'  => 'required|string',
            'access_type'  => 'required|in:view,edit',
            'value'        => 'required|boolean',
        ]);

        $schoolId = auth()->user()->school_id;

        // Verify user belongs to same school
        $user = User::findOrFail($request->user_id);
        if ($user->school_id !== $schoolId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $row = StaffModuleAccess::updateOrCreate(
            [
                'school_id'   => $schoolId,
                'user_id'     => $request->user_id,
                'module_key'  => $request->module_key,
                'feature_key' => $request->feature_key,
            ],
            [
                "{$request->access_type}_access" => $request->value,
            ]
        );

        return response()->json(['success' => true, 'row' => $row]);
    }

    /**
     * Get all staff for a module+feature cell (for the slide-in panel).
     */
    public function getStaffForCell(Request $request)
    {
        $request->validate([
            'module_key'  => 'required|string',
            'feature_key' => 'required|string',
            'access_type' => 'required|in:view,edit',
        ]);

        $schoolId = auth()->user()->school_id;

        $staff = User::where('school_id', $schoolId)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['teacher', 'accountant', 'school_admin']))
            ->with('roles')
            ->orderBy('name')
            ->get();

        // Defensive: guard against missing table
        $granted = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('staff_module_access')) {
            $accessCol = "{$request->access_type}_access";
            $granted = StaffModuleAccess::where('school_id', $schoolId)
                ->where('module_key', $request->module_key)
                ->where('feature_key', $request->feature_key)
                ->where($accessCol, true)
                ->pluck('user_id')
                ->toArray();
        }

        $result = $staff->map(fn ($u) => [
            'id'      => $u->id,
            'name'    => $u->name,
            'role'    => ucfirst(str_replace('_', ' ', $u->roles->first()?->name ?? 'Staff')),
            'initials'=> strtoupper(substr($u->name, 0, 1) . (str_contains($u->name, ' ') ? substr($u->name, strrpos($u->name, ' ') + 1, 1) : '')),
            'granted' => in_array($u->id, $granted),
        ]);

        return response()->json(['staff' => $result]);
    }

    /**
     * Save all staff selections for a module+feature cell (bulk save from panel).
     */
    public function saveStaffCell(Request $request)
    {
        $request->validate([
            'module_key'  => 'required|string',
            'feature_key' => 'required|string',
            'access_type' => 'required|in:view,edit',
            'user_ids'    => 'array',
            'user_ids.*'  => 'integer',
        ]);

        // Defensive: guard against missing table
        if (!\Illuminate\Support\Facades\Schema::hasTable('staff_module_access')) {
            return response()->json(['success' => false, 'error' => 'Table not yet created. Please run /fix-tables first.'], 503);
        }

        $schoolId   = auth()->user()->school_id;
        $accessCol  = "{$request->access_type}_access";
        $grantedIds = $request->input('user_ids', []);

        // Get all staff for this school
        $allStaffIds = User::where('school_id', $schoolId)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['teacher', 'accountant', 'school_admin']))
            ->pluck('id')
            ->toArray();

        foreach ($allStaffIds as $userId) {
            StaffModuleAccess::updateOrCreate(
                [
                    'school_id'   => $schoolId,
                    'user_id'     => $userId,
                    'module_key'  => $request->module_key,
                    'feature_key' => $request->feature_key,
                ],
                [
                    $accessCol => in_array($userId, $grantedIds),
                ]
            );
        }

        return response()->json(['success' => true, 'count' => count($grantedIds)]);
    }
}
