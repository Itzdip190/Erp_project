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
        // No auto-seeding
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
