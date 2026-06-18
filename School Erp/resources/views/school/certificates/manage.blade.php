@extends('layouts.app')

@section('page-title', 'Manage Certificates')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-stamp" style="color:var(--gold);margin-right:8px;"></i>Issue & Manage Certificates</h1>
        <p>Issue certificate documents to students, view logs, and preview printable layouts</p>
    </div>
</div>

<div class="grid-3">
    <!-- Issue Certificate Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Issue Certificate</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.certificates.manage') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Student</label>
                    <select name="student_id" id="certStudentSel" class="form-control" onchange="loadCertStudentPreview(this.value)" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Certificate Template</label>
                    <select name="certificate_template_id" id="certTemplateSel" class="form-control" onchange="updateCertTheme(this.value)" required>
                        <option value="">Select Template</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ ucfirst($tpl->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Issue Date</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-stamp"></i> Issue Certificate
                </button>
            </form>
        </div>
    </div>

    <!-- Interactive Certificate Mock Preview -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Certificate Document Preview</h3>
            <button class="btn btn-outline" onclick="printGeneratedCert()">
                <i class="fas fa-print"></i> Print Document
            </button>
        </div>
        <div class="card-body" style="display:flex; justify-content:center; align-items:center; background:#cbd5e1; padding:30px;">
            <!-- Real CSS Certificate Preview -->
            <div id="certPreviewContainer" style="width:100%; max-width:550px; min-height:380px; border:8px double #1e293b; background:#fff; color:#0f172a; padding:30px; box-shadow:var(--shadow-lg); font-family:'Georgia', serif; text-align:center; position:relative;">
                <!-- Border decoration -->
                <div style="border:1px solid #94a3b8; height:100%; padding:20px;">
                    <h2 style="font-size:14px; text-transform:uppercase; letter-spacing:2px; margin:0 0 15px; color:#1e293b;" id="previewCertSchool">Yash International School</h2>
                    
                    <h1 style="font-size:20px; font-weight:800; text-transform:uppercase; color:var(--gold); margin:0 0 20px;" id="previewCertTitle">School Leaving Certificate</h1>
                    
                    <p style="font-size:13px; line-height:1.8; color:#334155; margin:0 0 30px; text-align:justify; text-justify:inter-word;" id="previewCertBody">
                        This is to certify that <strong id="previewCertStudentName">Aarav Sharma</strong>, son/daughter of parent guardians, has successfully cleared academic grades at this institution. During their stay, they displayed exceptional moral character and active academic participation.
                    </p>

                    <!-- Bottom Signatures -->
                    <div style="display:flex; justify-content:space-between; font-size:12px; margin-top:40px; font-family:'Inter', sans-serif;">
                        <div style="text-align:left;">
                            <div>Date of Issue: <strong id="previewCertDate">{{ date('Y-m-d') }}</strong></div>
                            <div>Ref ID: <strong id="previewCertNo">CERT-89718</strong></div>
                        </div>
                        <div style="text-align:center; border-top:1px solid #334155; width:150px; padding-top:4px; align-self:flex-end;">
                            Principal Signature
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mock data maps for previews
const studentDetails = {
    @foreach($students as $st)
    "{{ $st->id }}": {
        name: "{{ $st->full_name }}",
        id: "{{ $st->admission_id }}",
    },
    @endforeach
};

const templateDetails = {
    @foreach($templates as $tpl)
    "{{ $tpl->id }}": {
        title: "{{ $tpl->title_text }}",
        body: "{{ str_replace(["\r", "\n"], ' ', $tpl->body_text) }}"
    },
    @endforeach
};

function loadCertStudentPreview(studentId) {
    if(!studentId || !studentDetails[studentId]) return;
    const s = studentDetails[studentId];
    document.getElementById('previewCertStudentName').textContent = s.name;
    document.getElementById('previewCertNo').textContent = 'CERT-' + Math.floor(10000 + Math.random() * 90000);
    updateBodyText();
}

function updateCertTheme(templateId) {
    if(!templateId || !templateDetails[templateId]) return;
    const t = templateDetails[templateId];
    document.getElementById('previewCertTitle').textContent = t.title;
    updateBodyText();
}

function updateBodyText() {
    const studentSel = document.getElementById('cardStudentSel'); // Wait, certStudentSel
    const studentId = document.getElementById('certStudentSel').value;
    const templateId = document.getElementById('certTemplateSel').value;
    
    if(!studentId || !templateId) return;
    
    const s = studentDetails[studentId];
    const t = templateDetails[templateId];
    
    let body = t.body;
    body = body.replace('[Student_Name]', `<strong>${s.name}</strong>`);
    body = body.replace('[Admission_ID]', `<strong>${s.id}</strong>`);
    body = body.replace('[Parent_Name]', 'guardian parents');
    body = body.replace('[Grade_Class]', 'Grade Class');
    body = body.replace('[Admission_Date]', '2025-04-10');
    body = body.replace('[Session_Name]', '2025-2026');
    
    document.getElementById('previewCertBody').innerHTML = body;
}

function printGeneratedCert() {
    const printContents = document.getElementById('certPreviewContainer').outerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = `
        <div style="display:flex; justify-content:center; align-items:center; height:100vh; background:#fff;">
            ${printContents}
        </div>
    `;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
@endsection
