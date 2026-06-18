<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CertificateTemplate;
use App\Models\StudentCertificate;
use App\Models\Student;
use App\Models\SchoolClass;

class CertificateManagementController extends Controller
{
    private function ensureCertificatesSeeded($schoolId)
    {
        if (CertificateTemplate::where('school_id', $schoolId)->count() === 0) {
            CertificateTemplate::create([
                'school_id' => $schoolId,
                'name' => 'Transfer Certificate Template',
                'type' => 'transfer',
                'title_text' => 'SCHOOL LEAVING / TRANSFER CERTIFICATE',
                'body_text' => 'This is to certify that [Student_Name], son/daughter of [Parent_Name], was admitted to this institution on [Admission_Date]. They have successfully cleared grade [Grade_Class] and are leaving this school on good terms.',
            ]);
            CertificateTemplate::create([
                'school_id' => $schoolId,
                'name' => 'Character Certificate Template',
                'type' => 'character',
                'title_text' => 'CHARACTER CERTIFICATE',
                'body_text' => 'This is to certify that [Student_Name], bearing Admission ID [Admission_ID], is a bonafide student of this institution. During their stay, they displayed exceptional moral character, discipline, and active academic participation.',
            ]);
            CertificateTemplate::create([
                'school_id' => $schoolId,
                'name' => 'Academic Distinction Award',
                'type' => 'custom',
                'title_text' => 'CERTIFICATE OF ACADEMIC DISTINCTION',
                'body_text' => 'This award is presented to [Student_Name] in recognition of outstanding academic achievements and excellent GPA standings during the academic session [Session_Name].',
            ]);
        }

        if (StudentCertificate::where('school_id', $schoolId)->count() === 0) {
            $students = Student::where('school_id', $schoolId)->take(3)->get();
            $tpl = CertificateTemplate::where('school_id', $schoolId)->first();
            if ($tpl) {
                foreach ($students as $st) {
                    StudentCertificate::create([
                        'school_id' => $schoolId,
                        'student_id' => $st->id,
                        'certificate_template_id' => $tpl->id,
                        'certificate_number' => 'CERT-' . rand(10000, 99999),
                        'issue_date' => now()->subDays(5)->toDateString(),
                    ]);
                }
            }
        }
    }

    public function templateCreator(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureCertificatesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:100',
                'type' => 'required|string|in:transfer,character,custom',
                'title_text' => 'required|string|max:150',
                'body_text' => 'required|string',
            ]);

            CertificateTemplate::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'type' => $request->type,
                'title_text' => $request->title_text,
                'body_text' => $request->body_text,
            ]);

            return back()->with('success', 'Certificate Template created successfully!');
        }

        $templates = CertificateTemplate::where('school_id', $schoolId)->get();
        return view('school.certificates.template_creator', compact('templates'));
    }

    public function manageCertificates(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureCertificatesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'certificate_template_id' => 'required|exists:certificate_templates,id',
                'issue_date' => 'required|date',
            ]);

            StudentCertificate::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'certificate_template_id' => $request->certificate_template_id,
                'certificate_number' => 'CERT-' . rand(10000, 99999),
                'issue_date' => $request->issue_date,
            ]);

            return back()->with('success', 'Certificate issued successfully!');
        }

        $students = Student::where('school_id', $schoolId)->get();
        $templates = CertificateTemplate::where('school_id', $schoolId)->get();
        $certificates = StudentCertificate::where('school_id', $schoolId)->with(['student.class', 'template'])->get();

        return view('school.certificates.manage', compact('students', 'templates', 'certificates'));
    }

    public function classWiseStudentCertificate(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureCertificatesSeeded($schoolId);

        $selectedClassId = $request->get('class_id');
        $students = collect();

        if ($selectedClassId) {
            $students = Student::where('school_id', $schoolId)->where('class_id', $selectedClassId)->with(['class', 'section'])->get();
        }

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $templates = CertificateTemplate::where('school_id', $schoolId)->get();

        return view('school.certificates.class_wise', compact('classes', 'students', 'templates', 'selectedClassId'));
    }

    public function certificatesReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureCertificatesSeeded($schoolId);

        $issuedCount = StudentCertificate::where('school_id', $schoolId)->count();
        $templatesCount = CertificateTemplate::where('school_id', $schoolId)->count();

        $byType = StudentCertificate::where('student_certificates.school_id', $schoolId)
            ->join('certificate_templates', 'student_certificates.certificate_template_id', '=', 'certificate_templates.id')
            ->selectRaw('certificate_templates.type as type, COUNT(*) as count')
            ->groupBy('certificate_templates.type')
            ->get();

        $certificates = StudentCertificate::where('school_id', $schoolId)->with(['student.class', 'template'])->latest()->take(10)->get();

        return view('school.certificates.report', compact('issuedCount', 'templatesCount', 'byType', 'certificates'));
    }
}
