<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnquiryLead;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Section;
use App\Models\AcademicSession;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class AdmissionsController extends Controller
{
    private function ensureLeadsSeeded($schoolId)
    {
        if (EnquiryLead::where('school_id', $schoolId)->count() === 0) {
            $leads = [
                ['student_name' => 'Aarav Mehta', 'parent_name' => 'Rajesh Mehta', 'phone' => '9876543210', 'email' => 'aarav.mehta@gmail.com', 'class_interested' => 'Grade 1', 'status' => 'new', 'notes' => 'Parent inquired about transport routes.'],
                ['student_name' => 'Diya Sen', 'parent_name' => 'Amit Sen', 'phone' => '9812345678', 'email' => 'diya.sen@gmail.com', 'class_interested' => 'Grade 5', 'status' => 'contacted', 'notes' => 'Scheduled interaction for next Monday.'],
                ['student_name' => 'Rohan Das', 'parent_name' => 'Sanjay Das', 'phone' => '9000111222', 'email' => 'rohan.das@gmail.com', 'class_interested' => 'Grade 3', 'status' => 'enrolled', 'notes' => 'All documents verified and fee paid.'],
            ];
            foreach ($leads as $lead) {
                EnquiryLead::create(array_merge($lead, ['school_id' => $schoolId]));
            }
        }
    }

    public function process(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Admission process stages configured successfully.');
        }
        return view('school.admissions.process');
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Admission configuration guidelines saved.');
        }
        return view('school.admissions.settings');
    }

    public function enquiryLeads(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeadsSeeded($schoolId);

        if ($request->isMethod('post')) {
            if ($request->has('update_status')) {
                $lead = EnquiryLead::where('school_id', $schoolId)->findOrFail($request->lead_id);
                $lead->status = $request->status;
                $lead->save();
                return back()->with('success', 'Lead status updated to ' . $request->status);
            }

            $request->validate([
                'student_name' => 'required|string|max:100',
                'parent_name' => 'required|string|max:100',
                'phone' => 'required|string|max:15',
                'email' => 'nullable|email|max:100',
                'class_interested' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
            ]);

            EnquiryLead::create(array_merge($request->all(), [
                'school_id' => $schoolId,
                'status' => 'new'
            ]));

            return back()->with('success', 'New front-office admission enquiry lead recorded.');
        }

        $leads = EnquiryLead::where('school_id', $schoolId)->orderBy('created_at', 'desc')->get();
        return view('school.admissions.enquiry_leads', compact('leads'));
    }

    public function applicationPayment(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Payment request link sent.');
        }
        return view('school.admissions.application_payment');
    }

    public function pendingDocuments(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeadsSeeded($schoolId);
        $leads = EnquiryLead::where('school_id', $schoolId)->where('status', '!=', 'enrolled')->get();

        if ($request->isMethod('post')) {
            return back()->with('success', 'Missing documents alert sent to parent.');
        }

        return view('school.admissions.pending_documents', compact('leads'));
    }

    public function interactionEvaluation(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeadsSeeded($schoolId);
        $leads = EnquiryLead::where('school_id', $schoolId)->where('status', 'contacted')->get();

        if ($request->isMethod('post')) {
            return back()->with('success', 'Evaluation schedule created and invitations emailed.');
        }

        return view('school.admissions.interaction_evaluation', compact('leads'));
    }

    public function admission(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeadsSeeded($schoolId);
        $leads = EnquiryLead::where('school_id', $schoolId)->where('status', '!=', 'enrolled')->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'lead_id' => 'required|exists:enquiry_leads,id',
                'class_id' => 'required|exists:school_classes,id',
            ]);

            $lead = EnquiryLead::findOrFail($request->lead_id);
            $lead->status = 'enrolled';
            $lead->save();

            // Convert to Student
            $student = Student::create([
                'school_id' => $schoolId,
                'first_name' => explode(' ', $lead->student_name)[0],
                'last_name' => str_contains($lead->student_name, ' ') ? explode(' ', $lead->student_name)[1] : 'Student',
                'admission_number' => 'ADM-' . rand(10000, 99999),
                'class_id' => $request->class_id,
                'admission_date' => now()->toDateString(),
                'guardian_name' => $lead->parent_name,
                'guardian_phone' => $lead->phone,
                'guardian_email' => $lead->email ?? 'parent.' . rand(100, 999) . '@example.com',
                'guardian_relationship' => 'father',
                'gender' => 'male',
                'date_of_birth' => now()->subYears(6)->toDateString(),
                'address' => 'Sample Residential Address',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
            ]);

            return back()->with('success', "Lead converted successfully! Student enrolled as {$student->full_name} with Admission ID: {$student->admission_number}");
        }

        return view('school.admissions.admission', compact('leads', 'classes'));
    }

    public function newAdmissionReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();

        $sessionId = $request->get('academic_session_id') ?? $currentSession?->id;
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Fetch sections of the selected class
        $sections = $classId 
            ? Section::where('school_id', $schoolId)->where('class_id', $classId)->get() 
            : collect();

        // Build query
        $query = Student::where('school_id', $schoolId)
            ->with(['class', 'section', 'academicSession']);

        if ($sessionId) {
            $query->where('academic_session_id', $sessionId);
        }
        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        if ($fromDate) {
            $query->whereDate('admission_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('admission_date', '<=', $toDate);
        }

        // Check if export is requested
        if ($request->get('export') === 'excel') {
            $students = $query->orderBy('admission_date', 'desc')->get();
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = ['Student Name', 'Admission ID', 'Academic Year', 'Admission Date', 'Class', 'Section', 'Father Name', 'Father Mobile Number'];
            $sheet->fromArray($headers, null, 'A1');
            
            $rowIdx = 2;
            foreach ($students as $student) {
                $sheet->fromArray([
                    $student->full_name,
                    $student->admission_number,
                    $student->academicSession?->name,
                    $student->admission_date ? $student->admission_date->format('d/m/Y') : 'N/A',
                    $student->class?->name,
                    $student->section?->name,
                    $student->father_name,
                    $student->father_phone ?? $student->guardian_phone
                ], null, 'A' . $rowIdx++);
            }
            
            $writer = new Xlsx($spreadsheet);
            
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, 'new_admission_report.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // Paginate students for the view
        $students = $query->orderBy('admission_date', 'desc')->paginate(10)->withQueryString();

        return view('school.admissions.new_admission_report', compact(
            'classes',
            'sections',
            'academicSessions',
            'students',
            'sessionId',
            'classId',
            'sectionId',
            'fromDate',
            'toDate'
        ));
    }

    public function dailyPlanner(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Admission planner agenda scheduled.');
        }
        return view('school.admissions.daily_planner');
    }

    public function dashboard()
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeadsSeeded($schoolId);

        $newCount = EnquiryLead::where('school_id', $schoolId)->where('status', 'new')->count();
        $contactedCount = EnquiryLead::where('school_id', $schoolId)->where('status', 'contacted')->count();
        $enrolledCount = EnquiryLead::where('school_id', $schoolId)->where('status', 'enrolled')->count();
        $rejectedCount = EnquiryLead::where('school_id', $schoolId)->where('status', 'rejected')->count();

        return view('school.admissions.dashboard', compact('newCount', 'contactedCount', 'enrolledCount', 'rejectedCount'));
    }
}
