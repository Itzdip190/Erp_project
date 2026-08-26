<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentGatePass;
use App\Models\School;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GatePassController extends Controller
{
    /**
     * Show Gate Pass Generator (Split Screen with live preview).
     */
    public function create(Student $student, Request $request)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized access to student record.');
        }

        $school = School::find($schoolId);

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

        // Auto increment Gate Pass number (format: 2026/GP/1)
        $autoNumber = StudentGatePass::generateNextNumber($schoolId);

        // Current time formatted nicely
        $currentDateTime = now()->format('d M Y h:i a');
        $currentDateTimeRaw = now()->format('Y-m-d\TH:i');

        // Initial template
        $template = $request->query('template', 'classic');
        if (!in_array($template, ['classic', 'modern', 'minimal', 'portrait', 'landscape'])) {
            $template = 'classic';
        }

        // Available templates list
        $templates = [
            [
                'id'          => 'classic',
                'name'        => 'Classic Standard',
                'description' => 'Official standard school outpass format matching the board guidelines.',
                'badge'       => 'Default & Recommended',
                'color'       => '#2563eb',
                'icon'        => 'fa-certificate',
                'orientation' => 'Portrait (A4)',
            ],
            [
                'id'          => 'modern',
                'name'        => 'Modern Enterprise',
                'description' => 'Navy header with QR-code security verification, pill badges, and dual-accent styling.',
                'badge'       => 'QR Security',
                'color'       => '#1e40af',
                'icon'        => 'fa-qrcode',
                'orientation' => 'Portrait (A4)',
            ],
            [
                'id'          => 'minimal',
                'name'        => 'Minimal Clean',
                'description' => 'Sleek geometric borders, high density typography, and clean monochrome layout.',
                'badge'       => 'Clean Print',
                'color'       => '#475569',
                'icon'        => 'fa-file-alt',
                'orientation' => 'Portrait (A4)',
            ],
            [
                'id'          => 'portrait',
                'name'        => 'Compact Slip / Badge',
                'description' => 'Half-sheet slip format optimized for rapid front-desk thermal and slip printing.',
                'badge'       => 'Compact Half-Page',
                'color'       => '#0891b2',
                'icon'        => 'fa-receipt',
                'orientation' => 'Half Page',
            ],
            [
                'id'          => 'landscape',
                'name'        => 'Dual-Copy (School & Gate)',
                'description' => 'Side-by-side twin layout: School Copy on Left, Security Gate & Parent Copy on Right.',
                'badge'       => 'Twin Counterfoil',
                'color'       => '#059669',
                'icon'        => 'fa-columns',
                'orientation' => 'Landscape (A4)',
            ],
        ];

        // Guardian options helper
        $guardians = [
            'father' => [
                'type'     => 'father',
                'name'     => $student->father_name ?? '',
                'relation' => 'Father',
                'phone'    => $student->father_phone ?? '',
            ],
            'mother' => [
                'type'     => 'mother',
                'name'     => $student->mother_name ?? '',
                'relation' => 'Mother',
                'phone'    => $student->mother_phone ?? '',
            ],
            'guardian' => [
                'type'     => 'guardian',
                'name'     => $student->guardian_name ?? '',
                'relation' => $student->guardian_relationship ?? 'Guardian',
                'phone'    => $student->guardian_phone ?? '',
            ],
            'other' => [
                'type'     => 'other',
                'name'     => '',
                'relation' => '',
                'phone'    => '',
            ],
        ];

        return view('school.student.gate-pass.create', compact(
            'student',
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
            'template',
            'templates',
            'guardians'
        ));
    }

    /**
     * Store newly created Gate Pass and trigger automated multi-channel notifications.
     */
    public function store(Request $request, Student $student)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'gate_pass_number'     => 'required|string|max:100',
            'template'             => 'required|string|in:classic,modern,minimal,portrait,landscape',
            'pass_date'            => 'required',
            'reason'               => 'required|string|max:500',
            'guardian_type'        => 'required|string|in:father,mother,guardian,other',
            'guardian_name'        => 'required|string|max:255',
            'guardian_relation'    => 'required|string|max:100',
            'guardian_phone'       => 'nullable|string|max:50',
            'expected_return_time' => 'nullable|string|max:100',
            'remarks'              => 'nullable|string|max:1000',
            'approved_by'          => 'nullable|string|max:255',
            'status'               => 'nullable|string|in:draft,generated,issued,returned,cancelled',
            'school_name'          => 'nullable|string|max:255',
            'school_address'       => 'nullable|string|max:500',
            'school_city'          => 'nullable|string|max:100',
            'school_state'         => 'nullable|string|max:100',
            'school_pincode'       => 'nullable|string|max:20',
            'school_phone'         => 'nullable|string|max:50',
            'school_email'         => 'nullable|string|max:100',
        ]);

        $passDate = Carbon::parse($request->input('pass_date', now()));

        // Ensure unique gate_pass_number per school
        $gatePassNumber = $validated['gate_pass_number'];
        $exists = StudentGatePass::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('gate_pass_number', $gatePassNumber)
            ->exists();

        if ($exists) {
            // Generate a fresh unique sequence if already taken
            $gatePassNumber = StudentGatePass::generateNextNumber($schoolId);
        }

        // Student snapshot for permanent record preservation
        $studentSnapshot = [
            'full_name'        => $student->full_name,
            'admission_number' => $student->admission_number,
            'roll_number'      => $student->roll_number,
            'class_name'       => $student->class?->name,
            'section_name'     => $student->section?->name,
            'photo_url'        => $student->photo_url,
            'father_name'      => $student->father_name,
            'mother_name'      => $student->mother_name,
            'gender'           => $student->gender,
            'age'              => $student->detailed_age,
        ];

        $gatePass = DB::transaction(function () use ($schoolId, $student, $validated, $passDate, $gatePassNumber, $studentSnapshot, $request) {
            return StudentGatePass::create([
                'school_id'            => $schoolId,
                'student_id'           => $student->id,
                'academic_session_id'  => $student->academic_session_id,
                'gate_pass_number'     => $gatePassNumber,
                'pass_date'            => $passDate,
                'template'             => $validated['template'] ?? 'classic',
                'reason'               => $validated['reason'],
                'guardian_type'        => $validated['guardian_type'],
                'guardian_name'        => $validated['guardian_name'],
                'guardian_relation'    => $validated['guardian_relation'],
                'guardian_phone'       => $validated['guardian_phone'] ?? null,
                'expected_return_time' => $validated['expected_return_time'] ?? null,
                'actual_return_time'   => null,
                'status'               => $validated['status'] ?? 'issued',
                'remarks'              => $validated['remarks'] ?? null,
                'approved_by'          => $validated['approved_by'] ?? Auth::user()->name,
                'issued_by'            => Auth::id(),
                'school_name'          => $validated['school_name'] ?? null,
                'school_address'       => $validated['school_address'] ?? null,
                'school_city'          => $validated['school_city'] ?? null,
                'school_state'         => $validated['school_state'] ?? null,
                'school_pincode'       => $validated['school_pincode'] ?? null,
                'school_phone'         => $validated['school_phone'] ?? null,
                'school_email'         => $validated['school_email'] ?? null,
                'student_snapshot'     => $studentSnapshot,
                'meta_data'            => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);
        });

        // Trigger Multi-Channel Notifications via Central NotificationService
        try {
            $formattedTime = $passDate->format('d M Y, h:i A');
            
            // 1. School Admin / Front Desk Notification
            NotificationService::send([
                'school_id'      => $schoolId,
                'recipient_role' => 'school_admin',
                'title'          => 'Gate Pass Generated',
                'message'        => "Gate Pass #{$gatePass->gate_pass_number} issued for {$student->full_name} with {$gatePass->guardian_name} ({$gatePass->guardian_relation}). Reason: {$gatePass->reason}",
                'module'         => 'gate_pass',
                'type'           => 'gate_pass_issued',
                'related_id'     => $gatePass->id,
                'priority'       => 'high',
                'action_url'     => route('school.students.gate-passes.pdf', [$student->id, $gatePass->id]),
                'icon'           => 'fa-ticket-alt',
                'color'          => '#2563eb',
            ]);

            // 2. Parent / Guardian Notification (if student has linked parent account or phone)
            if ($student->guardian_email || $student->father_email || $student->mother_email) {
                NotificationService::send([
                    'school_id'      => $schoolId,
                    'recipient_role' => 'parent',
                    'title'          => 'Student Out Pass Issued',
                    'message'        => "Out Pass #{$gatePass->gate_pass_number} issued for {$student->full_name} on {$formattedTime} accompanied by {$gatePass->guardian_name}.",
                    'module'         => 'gate_pass',
                    'type'           => 'gate_pass_issued',
                    'related_id'     => $gatePass->id,
                    'priority'       => 'high',
                    'action_url'     => route('school.students.gate-passes.pdf', [$student->id, $gatePass->id]),
                    'icon'           => 'fa-door-open',
                    'color'          => '#10b981',
                ]);
            }

            // 3. Student Notification
            if ($student->user_id) {
                NotificationService::send([
                    'school_id'      => $schoolId,
                    'user_id'        => $student->user_id,
                    'recipient_role' => 'student',
                    'title'          => 'Gate Pass Issued',
                    'message'        => "Gate Pass #{$gatePass->gate_pass_number} has been generated for your departure.",
                    'module'         => 'gate_pass',
                    'type'           => 'gate_pass_issued',
                    'related_id'     => $gatePass->id,
                    'priority'       => 'normal',
                    'action_url'     => route('school.students.gate-passes.pdf', [$student->id, $gatePass->id]),
                    'icon'           => 'fa-ticket-alt',
                    'color'          => '#2563eb',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Gate Pass notification sending failed: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Gate pass created successfully.',
                'gate_pass'    => $gatePass,
                'redirect_url' => route('school.students.show', $student->id) . '?tab=gatepass',
                'pdf_url'      => route('school.students.gate-passes.pdf', [$student->id, $gatePass->id]),
                'print_url'    => route('school.students.gate-passes.print', [$student->id, $gatePass->id]),
            ]);
        }

        return redirect()
            ->route('school.students.show', $student->id)
            ->with('success', "Gate Pass #{$gatePass->gate_pass_number} generated successfully.")
            ->with('active_tab', 'gatepass');
    }

    /**
     * Download or stream PDF Gate Pass.
     */
    public function downloadPdf(Student $student, StudentGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId || $gatePass->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $school = School::find($schoolId);
        $paperOrientation = $gatePass->template === 'landscape' ? 'landscape' : 'portrait';

        $pdf = Pdf::loadView('school.student.gate-pass.pdf', compact('student', 'gatePass', 'school'))
            ->setPaper('a4', $paperOrientation)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $safeNum = str_replace(['/', '\\', ' '], '_', $gatePass->gate_pass_number);
        return $pdf->stream("Gate_Pass_{$safeNum}_{$student->admission_number}.pdf");
    }

    /**
     * Direct Printable view for browser printing.
     */
    public function print(Student $student, StudentGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId || $gatePass->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $school = School::find($schoolId);

        return view('school.student.gate-pass.print', compact('student', 'gatePass', 'school'));
    }

    /**
     * Show Gate Pass detail JSON (for view modal).
     */
    public function show(Student $student, StudentGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId || $gatePass->school_id !== $schoolId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $school = School::find($schoolId);

        return response()->json([
            'success'   => true,
            'gate_pass' => $gatePass,
            'student'   => $student,
            'school'    => $school,
            'pdf_url'   => route('school.students.gate-passes.pdf', [$student->id, $gatePass->id]),
            'print_url' => route('school.students.gate-passes.print', [$student->id, $gatePass->id]),
        ]);
    }

    /**
     * Update Gate Pass Status (Return, Cancel, Reissue).
     */
    public function updateStatus(Request $request, Student $student, StudentGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId || $gatePass->school_id !== $schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status'  => 'required|in:draft,generated,issued,returned,cancelled',
            'remarks' => 'nullable|string|max:500',
        ]);

        $status = $validated['status'];
        $gatePass->status = $status;
        
        if ($status === 'returned') {
            $gatePass->actual_return_time = now();
        }

        if (!empty($validated['remarks'])) {
            $gatePass->remarks = ($gatePass->remarks ? $gatePass->remarks . "\n" : '') . "[" . now()->format('d M Y h:i a') . "] " . $validated['remarks'];
        }

        $gatePass->save();

        return response()->json([
            'success' => true,
            'message' => "Gate pass status updated to " . ucfirst($status),
            'status'  => $status,
            'badge'   => $gatePass->status_badge,
        ]);
    }

    /**
     * Soft delete gate pass.
     */
    public function destroy(Student $student, StudentGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($student->school_id !== $schoolId || $gatePass->school_id !== $schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $gatePass->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Gate pass deleted successfully.',
            ]);
        }

        return redirect()
            ->route('school.students.show', $student->id)
            ->with('success', 'Gate pass record deleted successfully.')
            ->with('active_tab', 'gatepass');
    }
}
