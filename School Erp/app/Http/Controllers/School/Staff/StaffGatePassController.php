<?php

namespace App\Http\Controllers\School\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffGatePass;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffGatePassController extends Controller
{
    /**
     * Show Gate Pass Generator Screen for Staff.
     */
    public function create(Request $request, Staff $staff)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;

        // Ensure staff belongs to this school
        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized access to staff record.');
        }

        // Pre-fill school details
        $schoolName = setting('school_name', $school?->name ?? 'School Name', $schoolId);
        $schoolAddress = setting('school_address', $school?->address ?? '', $schoolId);
        $schoolPhone = setting('school_phone', $school?->phone ?? '', $schoolId);
        $schoolEmail = setting('school_email', $school?->email ?? '', $schoolId);
        $schoolCity = $school?->city ?? '';
        $schoolState = $school?->state ?? '';
        $schoolPincode = $school?->pincode ?? '';
        $schoolLogo = $school?->logo_base64 ?: ($school?->logo_url ?: null);
        $principalName = setting('principal_name', $school?->director_name ?? 'Principal / Authorized Signatory', $schoolId);

        // Auto increment Gate Pass number (format: 2026/SGP/1)
        $autoNumber = StaffGatePass::generateNextNumber($schoolId);

        // Current time formatted nicely
        $currentDateTime = now()->format('d M Y h:i a');
        $currentDateTimeRaw = now()->format('Y-m-d\TH:i');

        // Initial template
        $template = $request->query('template', 'classic');

        return view('school.staff.gate-pass.create', compact(
            'staff',
            'school',
            'schoolName',
            'schoolAddress',
            'schoolPhone',
            'schoolEmail',
            'schoolCity',
            'schoolState',
            'schoolPincode',
            'schoolLogo',
            'principalName',
            'autoNumber',
            'currentDateTime',
            'currentDateTimeRaw',
            'template'
        ));
    }

    /**
     * Store Generated Staff Gate Pass.
     */
    public function store(Request $request, Staff $staff)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;

        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized access to staff record.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'pass_date' => 'nullable|date',
            'expected_return_time' => 'nullable|string|max:50',
            'template' => 'nullable|string|in:classic,modern,minimal,portrait,landscape',
            'remarks' => 'nullable|string|max:1000',
            'approved_by' => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:255',
            'school_city' => 'nullable|string|max:100',
            'school_state' => 'nullable|string|max:100',
            'school_pincode' => 'nullable|string|max:20',
        ]);

        $passDate = !empty($validated['pass_date']) 
            ? Carbon::parse($validated['pass_date']) 
            : now();

        $autoNumber = StaffGatePass::generateNextNumber($schoolId);

        // Snapshot staff & school info for audit safety
        $staffSnapshot = [
            'full_name' => $staff->full_name,
            'employee_id' => $staff->employee_id,
            'designation' => optional($staff->designation)->name ?? 'Staff',
            'department' => optional($staff->department)->name ?? 'General',
            'phone' => $staff->phone,
            'email' => $staff->email,
            'photo' => $staff->photo,
        ];

        $gatePass = StaffGatePass::create([
            'school_id' => $schoolId,
            'staff_id' => $staff->id,
            'gate_pass_number' => $autoNumber,
            'pass_date' => $passDate,
            'template' => $validated['template'] ?? 'classic',
            'reason' => $validated['reason'],
            'expected_return_time' => $validated['expected_return_time'] ?? null,
            'status' => 'issued',
            'remarks' => $validated['remarks'] ?? null,
            'approved_by' => $validated['approved_by'] ?? ($school?->director_name ?? 'Principal'),
            'issued_by' => Auth::id(),
            'school_name' => $validated['school_name'] ?? $school?->name,
            'school_logo' => $school?->logo,
            'school_address' => $validated['school_address'] ?? $school?->address,
            'school_city' => $validated['school_city'] ?? $school?->city,
            'school_state' => $validated['school_state'] ?? $school?->state,
            'school_pincode' => $validated['school_pincode'] ?? $school?->pincode,
            'staff_snapshot' => $staffSnapshot,
        ]);

        // Send multi-channel notification
        try {
            if (class_exists(NotificationService::class)) {
                $notifData = [
                    'school_id' => $schoolId,
                    'title' => 'Staff Gate Pass Issued',
                    'message' => "Gate Pass #{$gatePass->gate_pass_number} issued for staff {$staff->full_name}. Reason: {$gatePass->reason}",
                    'type' => 'staff_gate_pass',
                    'data' => [
                        'staff_id' => $staff->id,
                        'gate_pass_id' => $gatePass->id,
                        'gate_pass_number' => $gatePass->gate_pass_number,
                    ],
                ];

                // Notify admin
                NotificationService::send(
                    targetUserOrRole: 'admin',
                    title: $notifData['title'],
                    message: $notifData['message'],
                    type: $notifData['type'],
                    data: $notifData['data'],
                    schoolId: $schoolId
                );

                // Notify staff if linked with a user account
                if ($staff->user_id) {
                    NotificationService::send(
                        targetUserOrRole: $staff->user_id,
                        title: 'Your Gate Pass Has Been Issued',
                        message: "Gate Pass #{$gatePass->gate_pass_number} is active for your early departure ({$gatePass->reason}).",
                        type: 'staff_gate_pass',
                        data: $notifData['data'],
                        schoolId: $schoolId
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to dispatch staff gate pass notifications: ' . $e->getMessage());
        }

        return redirect()->route('school.staff.show', ['staff' => $staff->id, 'tab' => 'gatepass'])
            ->with('success', "Staff Gate Pass #{$gatePass->gate_pass_number} generated and issued successfully!");
    }

    /**
     * Show Gate Pass detail (JSON or partial).
     */
    public function show(Staff $staff, StaffGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($staff->school_id !== $schoolId || $gatePass->school_id !== $schoolId || $gatePass->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access.');
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'gate_pass' => $gatePass->load('staff', 'issuedByUser'),
                'pdf_url' => route('school.staff.gate-passes.pdf', [$staff->id, $gatePass->id]),
                'print_url' => route('school.staff.gate-passes.print', [$staff->id, $gatePass->id]),
            ]);
        }

        return view('school.staff.gate-pass.print', [
            'staff' => $staff,
            'gatePass' => $gatePass,
            'school' => Auth::user()->school,
        ]);
    }

    /**
     * Download or Stream DomPDF for Staff Gate Pass.
     */
    public function downloadPdf(Staff $staff, StaffGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($staff->school_id !== $schoolId || $gatePass->school_id !== $schoolId || $gatePass->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access.');
        }

        $school = Auth::user()->school;

        $pdf = Pdf::loadView('school.staff.gate-pass.pdf', [
            'staff' => $staff,
            'gatePass' => $gatePass,
            'school' => $school,
        ])->setPaper('a4', 'portrait')->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $fileName = 'Staff_Gate_Pass_' . str_replace('/', '_', $gatePass->gate_pass_number) . '.pdf';
        return $pdf->stream($fileName);
    }

    /**
     * Print View for Staff Gate Pass.
     */
    public function print(Staff $staff, StaffGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($staff->school_id !== $schoolId || $gatePass->school_id !== $schoolId || $gatePass->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access.');
        }

        $school = Auth::user()->school;

        return view('school.staff.gate-pass.print', [
            'staff' => $staff,
            'gatePass' => $gatePass,
            'school' => $school,
        ]);
    }

    /**
     * Update Gate Pass Status (e.g. mark returned or cancelled).
     */
    public function updateStatus(Request $request, Staff $staff, StaffGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($staff->school_id !== $schoolId || $gatePass->school_id !== $schoolId || $gatePass->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:issued,returned,cancelled',
            'remarks' => 'nullable|string|max:500',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'returned') {
            $updateData['actual_return_time'] = now();
        }

        if (!empty($validated['remarks'])) {
            $updateData['remarks'] = ($gatePass->remarks ? $gatePass->remarks . ' | ' : '') . $validated['remarks'];
        }

        if ($validated['status'] === 'cancelled' && !empty($validated['cancellation_reason'])) {
            $updateData['cancellation_reason'] = $validated['cancellation_reason'];
        }

        $gatePass->update($updateData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Gate Pass status updated to " . ucfirst($validated['status']),
                'gate_pass' => $gatePass->fresh(),
            ]);
        }

        return back()->with('success', "Gate Pass status updated to " . ucfirst($validated['status']));
    }

    /**
     * Delete Gate Pass (Soft delete).
     */
    public function destroy(Staff $staff, StaffGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($staff->school_id !== $schoolId || $gatePass->school_id !== $schoolId || $gatePass->staff_id !== $staff->id) {
            abort(403, 'Unauthorized access.');
        }

        $gatePass->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff Gate Pass record deleted successfully.',
            ]);
        }

        return back()->with('success', 'Staff Gate Pass record deleted successfully.');
    }
}
