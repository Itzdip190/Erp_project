<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ModulePermission;
use App\Models\StaffModuleAccess;
use App\Models\User;
use App\Support\ModuleRegistry;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /* ─────────────────────────────────────────────────────────────────
     *  ROLE CATEGORY – module + feature view/edit toggles
     * ───────────────────────────────────────────────────────────────── */

    public function index()
    {
        $schoolId = auth()->user()->school_id;
        $modules  = ModuleRegistry::all();

        // Load saved permissions keyed by "moduleKey.featureKey"
        $saved = ModulePermission::where('school_id', $schoolId)
            ->get()
            ->keyBy(fn ($p) => "{$p->module_key}.{$p->feature_key}");

        return view('school.roles.index', compact('modules', 'saved'));
    }

    public function updateRolePermissions(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $modules  = ModuleRegistry::all();

        // Build expected feature list from registry
        foreach ($modules as $moduleKey => $module) {
            foreach ($module['features'] as $featureKey => $_) {
                $viewKey = "view_{$moduleKey}_{$featureKey}";
                $editKey = "edit_{$moduleKey}_{$featureKey}";

                ModulePermission::updateOrCreate(
                    [
                        'school_id'   => $schoolId,
                        'module_key'  => $moduleKey,
                        'feature_key' => $featureKey,
                    ],
                    [
                        'view_access' => $request->boolean($viewKey),
                        'edit_access' => $request->boolean($editKey),
                    ]
                );
            }
        }

        return back()->with('success', 'Role permissions saved successfully!');
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

        // Load saved staff access rows
        // Keyed: "userId.moduleKey.featureKey" => {view_access, edit_access}
        $access = StaffModuleAccess::where('school_id', $schoolId)
            ->get()
            ->keyBy(fn ($r) => "{$r->user_id}.{$r->module_key}.{$r->feature_key}");

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

        // Which staff already have this access
        $accessCol = "{$request->access_type}_access";
        $granted = StaffModuleAccess::where('school_id', $schoolId)
            ->where('module_key', $request->module_key)
            ->where('feature_key', $request->feature_key)
            ->where($accessCol, true)
            ->pluck('user_id')
            ->toArray();

        $result = $staff->map(fn ($u) => [
            'id'      => $u->id,
            'name'    => $u->name,
            'role'    => ucfirst(str_replace('_', ' ', $u->roles->first()?->name ?? 'Staff')),
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
            'user_ids.*'  => 'exists:users,id',
        ]);

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
