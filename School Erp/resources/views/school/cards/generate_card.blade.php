@extends('layouts.app')

@section('page-title', 'Generate Card')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-print" style="color:var(--gold);margin-right:8px;"></i>Generate ID/Bus/Admit Card</h1>
        <p>Issue new cards, preview card layouts, and print student card documents</p>
    </div>
</div>

<div class="grid-3">
    <!-- Generation Request Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Issue Student Card</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.cards.generate-card') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Student</label>
                    <select name="student_id" id="cardStudentSel" class="form-control" onchange="loadCardStudentPreview(this.value)" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Card Template</label>
                    <select name="card_template_id" id="cardTemplateSel" class="form-control" onchange="updateCardTheme(this.value)" required>
                        <option value="">Select Template</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ ucfirst(str_replace('_',' ',$tpl->type)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-id-badge"></i> Issue Card
                </button>
            </form>
        </div>
    </div>

    <!-- Interactive Card Mock Preview -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Card Layout Live Preview</h3>
            <button class="btn btn-outline" onclick="printGeneratedCard()">
                <i class="fas fa-print"></i> Print Card
            </button>
        </div>
        <div class="card-body" style="display:flex; justify-content:center; align-items:center; background:#f1f5f9; padding:40px;">
            <!-- Real CSS Card Preview -->
            <div id="cardPreviewContainer" style="width:320px; height:450px; border-radius:16px; background-color:#1a1f3c; color:#fff; display:flex; flex-direction:column; padding:20px; box-shadow:var(--shadow-lg); transition:all 0.3s ease; font-family:'Inter', sans-serif;">
                <!-- Header -->
                <div style="text-align:center; border-bottom:1px solid rgba(255,255,255,0.15); padding-bottom:10px; margin-bottom:15px;">
                    <h4 style="font-size:13px; font-weight:800; text-transform:uppercase; margin:0; letter-spacing:0.5px;">Yash International School</h4>
                    <span style="font-size:9px; opacity:0.75; letter-spacing:1px; text-transform:uppercase;" id="previewCardType">Student Identity Card</span>
                </div>
                
                <!-- Card Photo Placeholder -->
                <div style="display:flex; justify-content:center; margin-bottom:15px;">
                    <div style="width:110px; height:130px; border-radius:8px; border:2px solid rgba(255,255,255,0.2); background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:40px;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <!-- Info Details -->
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; text-align:center;">
                    <h3 style="font-size:16px; font-weight:800; margin-bottom:4px;" id="previewStudentName">Aarav Sharma</h3>
                    <span style="font-size:11px; opacity:0.8; margin-bottom:12px;" id="previewStudentClass">Grade: Class 10 - Section A</span>
                    
                    <div style="width:100%; border-top:1px solid rgba(255,255,255,0.1); padding-top:10px; text-align:left; font-size:10.5px; line-height:1.6;">
                        <div style="display:flex; justify-content:space-between;"><span>Admission ID:</span><strong id="previewStudentID">YIS/2026/00001</strong></div>
                        <div style="display:flex; justify-content:space-between;"><span>Card Number:</span><strong id="previewCardNo">CRD-782618</strong></div>
                        <div style="display:flex; justify-content:space-between;"><span>Expiry Date:</span><strong id="previewCardExpiry">{{ date('Y-m-d', strtotime('+1 year')) }}</strong></div>
                    </div>
                </div>

                <!-- Bottom QR Stub -->
                <div style="display:flex; justify-content:center; margin-top:auto; padding-top:10px; border-top:1px solid rgba(255,255,255,0.15);">
                    <div style="width:40px; height:40px; background:#fff; padding:3px; border-radius:4px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-qrcode" style="color:#000; font-size:34px;"></i>
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
        class: "{{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}",
        id: "{{ $st->admission_id }}",
    },
    @endforeach
};

const templateDetails = {
    @foreach($templates as $tpl)
    "{{ $tpl->id }}": {
        bg: "{{ $tpl->background_color }}",
        text: "{{ $tpl->text_color }}",
        type: "{{ ucfirst(str_replace('_', ' ', $tpl->type)) }}"
    },
    @endforeach
};

function loadCardStudentPreview(studentId) {
    if(!studentId || !studentDetails[studentId]) return;
    const s = studentDetails[studentId];
    document.getElementById('previewStudentName').textContent = s.name;
    document.getElementById('previewStudentClass').textContent = 'Grade: ' + s.class;
    document.getElementById('previewStudentID').textContent = s.id;
    document.getElementById('previewCardNo').textContent = 'CRD-' + Math.floor(100000 + Math.random() * 900000);
}

function updateCardTheme(templateId) {
    if(!templateId || !templateDetails[templateId]) return;
    const t = templateDetails[templateId];
    const card = document.getElementById('cardPreviewContainer');
    card.style.backgroundColor = t.bg;
    card.style.color = t.text;
    document.getElementById('previewCardType').textContent = t.type;
}

function printGeneratedCard() {
    const printContents = document.getElementById('cardPreviewContainer').outerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = `
        <div style="display:flex; justify-content:center; align-items:center; height:100vh;">
            ${printContents}
        </div>
    `;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
@endsection
