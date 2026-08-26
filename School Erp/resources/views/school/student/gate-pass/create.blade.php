@extends('layouts.app')

@section('title', 'Student Gate Pass Generator - ' . $student->full_name)

@section('content')
<style>
    :root {
        --accent: #1d4ed8;
        --accent-rgb: 29, 78, 216;
        --navy-dark: #0f172a;
    }

    .split-container {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        margin-top: 1rem;
    }

    @media (max-width: 1024px) {
        .split-container {
            flex-direction: column;
        }
        .form-column, .preview-column {
            width: 100% !important;
            max-width: 100% !important;
        }
    }

    /* Left Form Column */
    .form-column {
        flex: 1;
        min-width: 360px;
        max-width: 440px;
        background: var(--card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    /* Right Preview Column */
    .preview-column {
        flex: 1.6;
        min-width: 380px;
        background: #e2e8f0;
        border: 1px solid var(--border, #cbd5e1);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: calc(100vh - 120px);
        position: relative;
        overflow: hidden;
    }

    body.dark-mode .preview-column {
        background: #0f172a;
        border-color: #1e293b;
    }

    .preview-canvas-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
        overflow: auto;
        padding: 10px 0 60px 0;
        transition: transform 0.2s ease;
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--accent);
        margin: 1.5rem 0 0.75rem 0;
        padding-bottom: 4px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-section-title:first-of-type {
        margin-top: 0;
    }

    .field-group {
        margin-bottom: 1rem;
    }

    .field-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--t1, #334155);
        margin-bottom: 5px;
        display: block;
    }

    .field-input, .field-select, .field-textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--border, #cbd5e1);
        border-radius: 8px;
        font-size: 13px;
        background: var(--bg-card, #ffffff);
        color: var(--t1, #0f172a);
        font-family: inherit;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .field-input:focus, .field-select:focus, .field-textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.15);
    }

    .guardian-pills {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
        margin-bottom: 12px;
    }

    .guardian-pill-btn {
        padding: 7px 4px;
        font-size: 11.5px;
        font-weight: 700;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: var(--bg-card, #ffffff);
        color: var(--t2, #64748b);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .guardian-pill-btn.active {
        background: var(--accent);
        color: #ffffff;
        border-color: var(--accent);
        box-shadow: 0 2px 6px rgba(var(--accent-rgb), 0.25);
    }

    /* Floating Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: var(--card, #ffffff);
        border: 1px solid var(--border, #cbd5e1);
        border-radius: 30px;
        padding: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        z-index: 10;
    }

    .zoom-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: var(--t1, #334155);
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .zoom-btn:hover {
        background: rgba(0,0,0,0.06);
    }

    /* Template Selection Modal Cards */
    .template-card {
        border: 2px solid var(--border, #e2e8f0);
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: var(--bg-card, #ffffff);
    }

    .template-card:hover {
        border-color: var(--accent);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -5px rgba(29, 78, 216, 0.15);
    }

    .template-card.active {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.04);
        box-shadow: 0 0 0 2px rgba(var(--accent-rgb), 0.3);
    }

    .template-card.active::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 10px;
        right: 10px;
        width: 22px;
        height: 22px;
        background: var(--accent);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
</style>

<!-- Top Action Header -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1rem;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <a href="{{ route('school.students.show', $student->id) }}?tab=gatepass" style="color:var(--text-muted); text-decoration:none; font-size:13px; font-weight:600;">
                <i class="fa fa-arrow-left"></i> Student 360°
            </a>
            <span style="color:var(--border);">&bull;</span>
            <span style="font-size:13px; color:var(--accent); font-weight:700;">Gate Pass Management</span>
        </div>
        <h2 style="font-family:'Syne', sans-serif; font-weight:800; font-size:1.6rem; color:var(--t1); margin:0;">
            Issue Gate Pass
        </h2>
    </div>

    <!-- Action Buttons -->
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <button type="button" onclick="openTemplateModal()" class="btn" style="background:#ffffff; color:var(--t1); border:1px solid var(--border); padding:9px 16px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
            <i class="fa fa-th-large" style="color:var(--accent);"></i>
            <span>Change Template</span>
            <span id="activeTemplateBadge" class="badge" style="background:rgba(29, 78, 216, 0.1); color:var(--accent); font-size:11px; padding:2px 8px; border-radius:4px; text-transform:capitalize;">{{ $template }}</span>
        </button>

        <button type="button" onclick="submitGatePassForm('issued')" class="btn-accent" style="background:var(--accent); color:#ffffff; padding:10px 22px; border-radius:10px; font-size:13.5px; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(var(--accent-rgb), 0.3);">
            <i class="fa fa-check-circle"></i> Create Gatepass
        </button>
    </div>
</div>

<!-- Main Split Screen -->
<div class="split-container">
    
    <!-- LEFT: Form Column -->
    <div class="form-column">
        <form id="gatePassForm" action="{{ route('school.students.gate-passes.store', $student->id) }}" method="POST">
            @csrf
            <input type="hidden" name="template" id="inputTemplate" value="{{ $template }}">
            <input type="hidden" name="status" id="inputStatus" value="issued">

            <!-- Section 1: School Profile (Auto-filled) -->
            <div class="form-section-title">
                <i class="fa fa-school"></i> School Details (Auto-filled)
            </div>

            <div class="field-group">
                <label class="field-label">School Name</label>
                <input type="text" name="school_name" id="inpSchoolName" class="field-input live-sync" value="{{ $schoolName }}" placeholder="School Name">
            </div>

            <div class="field-group">
                <label class="field-label">School Image</label>
                <div style="display:flex; align-items:center; gap:12px; background:rgba(0,0,0,0.02); padding:8px 12px; border-radius:8px; border:1px solid var(--border);">
                    <div style="width:52px; height:52px; border-radius:8px; border:1px solid var(--border); background:#ffffff; display:flex; align-items:center; justify-content:center; overflow:hidden; padding:3px; flex-shrink:0;">
                        @if(!empty($schoolLogo))
                            <img src="{{ $schoolLogo }}" alt="School Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" style="max-width:100%; max-height:100%; object-fit:contain;">
                            <div style="display:none; color:var(--accent); font-size:20px;"><i class="fa fa-graduation-cap"></i></div>
                        @else
                            <div style="color:var(--accent); font-size:20px;"><i class="fa fa-graduation-cap"></i></div>
                        @endif
                    </div>
                    <div style="font-size:11.5px; color:var(--text-muted); line-height:1.4;">
                        <strong style="color:var(--t1); font-size:12px;">Auto-synced School Crest</strong><br>
                        Displays on all printed passes & PDFs
                    </div>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">School Address</label>
                <input type="text" name="school_address" id="inpSchoolAddress" class="field-input live-sync" value="{{ $schoolAddress }}" placeholder="Campus Address">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                <div class="field-group">
                    <label class="field-label">City</label>
                    <input type="text" name="school_city" id="inpSchoolCity" class="field-input live-sync" value="{{ $schoolCity }}" placeholder="City">
                </div>
                <div class="field-group">
                    <label class="field-label">State</label>
                    <input type="text" name="school_state" id="inpSchoolState" class="field-input live-sync" value="{{ $schoolState }}" placeholder="State">
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">PIN Code</label>
                <input type="text" name="school_pincode" id="inpSchoolPincode" class="field-input live-sync" value="{{ $schoolPincode }}" placeholder="PIN Code">
            </div>

            <!-- Section 2: Student Details (Auto-filled) -->
            <div class="form-section-title">
                <i class="fa fa-user-graduate"></i> Student Details (Auto-filled)
            </div>

            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px; background:rgba(0,0,0,0.02); padding:10px; border-radius:8px; border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:8px; overflow:hidden; border:1px solid var(--border); flex-shrink:0; background:#f8fafc;">
                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div>
                    <strong style="font-size:13.5px; color:var(--t1);">{{ $student->full_name }}</strong>
                    <div style="font-size:11.5px; color:var(--text-muted);">
                        Adm No: <strong>{{ $student->admission_number }}</strong> &bull; Class: <strong>{{ $student->class?->name }} - {{ $student->section?->name }}</strong>
                    </div>
                </div>
            </div>

            <!-- Section 3: Guardian Details & Selection -->
            <div class="form-section-title">
                <i class="fa fa-user-shield"></i> Accompanying Guardian
            </div>

            <label class="field-label">Select Accompanying Person</label>
            <div class="guardian-pills">
                <button type="button" class="guardian-pill-btn active" onclick="selectGuardian('father')" id="pill-father">Father</button>
                <button type="button" class="guardian-pill-btn" onclick="selectGuardian('mother')" id="pill-mother">Mother</button>
                <button type="button" class="guardian-pill-btn" onclick="selectGuardian('guardian')" id="pill-guardian">Guardian</button>
                <button type="button" class="guardian-pill-btn" onclick="selectGuardian('other')" id="pill-other">Other</button>
            </div>
            <input type="hidden" name="guardian_type" id="inputGuardianType" value="father">

            <div class="field-group">
                <label class="field-label">Guardian Name *</label>
                <input type="text" name="guardian_name" id="inpGuardianName" class="field-input live-sync" value="{{ $guardians['father']['name'] ?: ($student->guardian_name ?: '') }}" required placeholder="Enter Guardian Name">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                <div class="field-group">
                    <label class="field-label">Relation with Student *</label>
                    <input type="text" name="guardian_relation" id="inpGuardianRelation" class="field-input live-sync" value="Father" required placeholder="e.g. Father">
                </div>
                <div class="field-group">
                    <label class="field-label">Guardian Mobile</label>
                    <input type="text" name="guardian_phone" id="inpGuardianPhone" class="field-input live-sync" value="{{ $guardians['father']['phone'] ?: ($student->guardian_phone ?: '') }}" placeholder="Mobile Number">
                </div>
            </div>

            <!-- Section 4: Gate Pass Specifics -->
            <div class="form-section-title">
                <i class="fa fa-ticket-alt"></i> Gate Pass Specifics
            </div>

            <div class="field-group">
                <label class="field-label">Reason for Early Departure *</label>
                <select id="selReasonPreset" class="field-select" onchange="onReasonPresetChange(this.value)" style="margin-bottom:6px;">
                    <option value="Medical Checkup / Illness" selected>Medical Checkup / Illness</option>
                    <option value="Family Emergency">Family Emergency</option>
                    <option value="Early Departure (Personal Reason)">Early Departure (Personal Reason)</option>
                    <option value="Doctor / Hospital Appointment">Doctor / Hospital Appointment</option>
                    <option value="Official School Representation / Sports">Official School Representation / Sports</option>
                    <option value="Parent Request / Out of Town">Parent Request / Out of Town</option>
                    <option value="custom">-- Type Custom Reason --</option>
                </select>
                <input type="text" name="reason" id="inpReason" class="field-input live-sync" value="Medical Checkup / Illness" required placeholder="Describe Reason">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                <div class="field-group">
                    <label class="field-label">Auto Ref Number</label>
                    <input type="text" name="gate_pass_number" id="inpRefNo" class="field-input live-sync" value="{{ $autoNumber }}" required>
                </div>
                <div class="field-group">
                    <label class="field-label">Date & Time</label>
                    <input type="text" name="pass_date" id="inpPassDate" class="field-input live-sync" value="{{ $currentDateTime }}" required>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Expected Return Time (Optional)</label>
                <input type="text" name="expected_return_time" id="inpReturnTime" class="field-input live-sync" placeholder="e.g. 03:30 PM or Will Not Return">
            </div>

            <div class="field-group">
                <label class="field-label">Remarks / Special Notes (Optional)</label>
                <textarea name="remarks" id="inpRemarks" class="field-textarea live-sync" rows="2" placeholder="Any special instructions or observations"></textarea>
            </div>

            <div class="field-group">
                <label class="field-label">Approved By</label>
                <input type="text" name="approved_by" id="inpApprovedBy" class="field-input live-sync" value="{{ auth()->user()->name }}" placeholder="Approver Name">
            </div>
        </form>
    </div>

    <!-- RIGHT: Live Preview Column -->
    <div class="preview-column">
        
        <!-- Live Preview Canvas -->
        <div class="preview-canvas-wrapper" id="previewWrapper">
            <div id="previewContainer" style="transform-origin: top center; transition: transform 0.2s ease;">
                
                <!-- Dynamic Template Inclusions -->
                <div id="template-classic" class="template-view" style="{{ $template === 'classic' ? '' : 'display:none;' }}">
                    @include('school.student.gate-pass.templates.classic')
                </div>

                <div id="template-modern" class="template-view" style="{{ $template === 'modern' ? '' : 'display:none;' }}">
                    @include('school.student.gate-pass.templates.modern')
                </div>

                <div id="template-minimal" class="template-view" style="{{ $template === 'minimal' ? '' : 'display:none;' }}">
                    @include('school.student.gate-pass.templates.minimal')
                </div>

                <div id="template-portrait" class="template-view" style="{{ $template === 'portrait' ? '' : 'display:none;' }}">
                    @include('school.student.gate-pass.templates.portrait')
                </div>

                <div id="template-landscape" class="template-view" style="{{ $template === 'landscape' ? '' : 'display:none;' }}">
                    @include('school.student.gate-pass.templates.landscape')
                </div>

            </div>
        </div>

        <!-- Floating Zoom Controls -->
        <div class="zoom-controls">
            <button type="button" class="zoom-btn" onclick="adjustZoom(-0.1)" title="Zoom Out"><i class="fa fa-minus"></i></button>
            <span id="zoomLabel" style="font-size:11px; font-weight:700; padding:0 4px; color:var(--t1);">100%</span>
            <button type="button" class="zoom-btn" onclick="adjustZoom(0.1)" title="Zoom In"><i class="fa fa-plus"></i></button>
            <button type="button" class="zoom-btn" onclick="resetZoom()" title="Reset Zoom"><i class="fa fa-redo" style="font-size:11px;"></i></button>
        </div>
    </div>

</div>

<!-- TEMPLATE SELECTION MODAL -->
<div id="templateSelectionModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); z-index:99999; align-items:center; justify-content:center; padding:1.5rem;">
    <div style="background:var(--bg-card, #ffffff); border-radius:18px; width:100%; max-width:840px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); overflow:hidden; border:1px solid var(--border);">
        
        <!-- Modal Header -->
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="font-family:'Syne', sans-serif; font-size:1.3rem; font-weight:800; margin:0; color:var(--t1);">
                    Select Gate Pass Template
                </h3>
                <p style="font-size:12.5px; color:var(--text-muted); margin:3px 0 0 0;">
                    Choose an official layout for printing and PDF generation.
                </p>
            </div>
            <button type="button" onclick="closeTemplateModal()" style="background:rgba(0,0,0,0.05); border:none; width:36px; height:36px; border-radius:8px; cursor:pointer; font-size:16px; color:var(--t1); display:flex; align-items:center; justify-content:center;">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <!-- Modal Body: Templates Grid -->
        <div style="padding:1.5rem; overflow-y:auto; display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px;">
            @foreach($templates as $tmpl)
                <div class="template-card {{ $template === $tmpl['id'] ? 'active' : '' }}" onclick="selectTemplate('{{ $tmpl['id'] }}', '{{ $tmpl['name'] }}')" id="tmpl-card-{{ $tmpl['id'] }}">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                        <div style="width:36px; height:36px; border-radius:8px; background:rgba(29, 78, 216, 0.1); color:{{ $tmpl['color'] }}; display:flex; align-items:center; justify-content:center; font-size:16px;">
                            <i class="fa {{ $tmpl['icon'] }}"></i>
                        </div>
                        <div>
                            <strong style="font-size:14px; color:var(--t1); display:block;">{{ $tmpl['name'] }}</strong>
                            <span style="font-size:10.5px; color:var(--text-muted); font-weight:600;">{{ $tmpl['orientation'] }}</span>
                        </div>
                    </div>
                    <p style="font-size:12px; color:var(--text-muted); margin:0 0 10px 0; line-height:1.4;">
                        {{ $tmpl['description'] }}
                    </p>
                    <span class="badge" style="background:rgba(0,0,0,0.05); color:var(--t1); font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px;">
                        {{ $tmpl['badge'] }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Modal Footer -->
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:rgba(0,0,0,0.015);">
            <button type="button" onclick="closeTemplateModal()" class="btn" style="padding:8px 18px; border-radius:8px; font-weight:700; font-size:13px; border:1px solid var(--border); background:transparent; color:var(--t1); cursor:pointer;">
                Done
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const guardiansData = @json($guardians);
    let currentZoom = 1.0;

    // Synchronize inputs live into preview elements
    function syncAllFields() {
        const schoolName = $('#inpSchoolName').val();
        const schoolAddr = $('#inpSchoolAddress').val();
        const schoolCity = $('#inpSchoolCity').val();
        const schoolState = $('#inpSchoolState').val();
        const schoolPin = $('#inpSchoolPincode').val();
        const guardianName = $('#inpGuardianName').val();
        const guardianRel = $('#inpGuardianRelation').val();
        const guardianPhone = $('#inpGuardianPhone').val();
        const reason = $('#inpReason').val();
        const refNo = $('#inpRefNo').val();
        const passDate = $('#inpPassDate').val();
        const returnTime = $('#inpReturnTime').val();
        const remarks = $('#inpRemarks').val();

        // 1. Classic Template Sync
        $('#pv-school-name').text(schoolName || 'School Name');
        $('#pv-school-address').text(schoolAddr || '');
        $('#pv-school-city-state').text((schoolCity ? ', ' + schoolCity : '') + (schoolState ? ', ' + schoolState : ''));
        if (schoolPin) {
            $('#pv-school-pincode').text(schoolPin).show();
        } else {
            $('#pv-school-pincode').hide();
        }
        $('#pv-ref-no').text(refNo || '2026/GP/1');
        $('#pv-date').text(passDate || '');
        $('#pv-guardian-name').text(guardianName || 'Guardian');
        $('#pv-guardian-relation').text(guardianRel || 'Guardian');
        $('#pv-reason').text(reason || 'Medical Checkup / Illness');

        if (returnTime) {
            $('#pv-return-time').text(returnTime);
            $('#pv-row-return-time').show();
        } else {
            $('#pv-row-return-time').hide();
        }

        if (remarks) {
            $('#pv-remarks').text(remarks);
            $('#pv-row-remarks').show();
        } else {
            $('#pv-row-remarks').hide();
        }

        // 2. Modern Template Sync
        $('#pv-school-name-modern').text(schoolName || 'School Name');
        $('#pv-school-address-modern').text(schoolAddr || '');
        $('#pv-school-city-state-modern').text((schoolCity ? ', ' + schoolCity : '') + (schoolState ? ', ' + schoolState : ''));
        $('#pv-school-pincode-modern').text(schoolPin || '');
        $('#pv-ref-no-modern').text(refNo || '2026/GP/1');
        $('#pv-date-modern').text(passDate || '');
        $('#pv-guardian-name-modern').text(guardianName || 'Guardian');
        $('#pv-guardian-relation-modern').text(guardianRel || 'Guardian');
        $('#pv-guardian-phone-modern').text(guardianPhone || 'N/A');
        $('#pv-reason-modern').text(reason || 'Medical Checkup / Illness');

        if (returnTime) {
            $('#pv-return-time-modern').text(returnTime);
            $('#pv-return-time-box-modern').show();
        } else {
            $('#pv-return-time-box-modern').hide();
        }

        if (remarks) {
            $('#pv-remarks-modern').text(remarks);
            $('#pv-remarks-box-modern').show();
        } else {
            $('#pv-remarks-box-modern').hide();
        }

        // 3. Minimal Template Sync
        $('#pv-school-name-minimal').text(schoolName || 'School Name');
        $('#pv-school-address-minimal').text(schoolAddr || '');
        $('#pv-school-city-state-minimal').text((schoolCity ? ', ' + schoolCity : ''));
        $('#pv-ref-no-minimal').text(refNo || '2026/GP/1');
        $('#pv-date-minimal').text(passDate || '');
        $('#pv-guardian-name-minimal').text(guardianName || 'Guardian');
        $('#pv-guardian-relation-minimal').text(guardianRel || 'Guardian');
        $('#pv-reason-minimal').text(reason || 'Medical Checkup / Illness');

        // 4. Portrait Template Sync
        $('#pv-school-name-portrait').text(schoolName || 'School Name');
        $('#pv-school-address-portrait').text(schoolAddr || '');
        $('#pv-ref-no-portrait').text(refNo || '2026/GP/1');
        $('#pv-date-portrait').text(passDate || '');
        $('#pv-guardian-name-portrait').text(guardianName || 'Guardian');
        $('#pv-guardian-relation-portrait').text(guardianRel || 'Guardian');
        $('#pv-reason-portrait').text(reason || 'Medical Checkup / Illness');

        // 5. Landscape Template Sync
        $('#pv-school-name-land1, #pv-school-name-land2').text(schoolName || 'School Name');
        $('#pv-school-address-land1, #pv-school-address-land2').text(schoolAddr || '');
        $('#pv-ref-no-land1, #pv-ref-no-land2').text(refNo || '2026/GP/1');
        $('#pv-date-land1, #pv-date-land2').text(passDate || '');
        $('#pv-guardian-name-land1, #pv-guardian-name-land2').text(guardianName || 'Guardian');
        $('#pv-guardian-relation-land1, #pv-guardian-relation-land2').text(guardianRel || 'Guardian');
        $('#pv-reason-land1, #pv-reason-land2').text(reason || 'Medical / Illness');
    }

    // Bind event listeners for real-time reactivity
    $(document).on('input keyup change', '.live-sync', function() {
        syncAllFields();
    });

    // Preset reason selection
    function onReasonPresetChange(val) {
        if (val === 'custom') {
            $('#inpReason').val('').focus();
        } else {
            $('#inpReason').val(val);
        }
        syncAllFields();
    }

    // Guardian Quick Selection
    function selectGuardian(type) {
        $('.guardian-pill-btn').removeClass('active');
        $('#pill-' + type).addClass('active');
        $('#inputGuardianType').val(type);

        const g = guardiansData[type] || {};
        if (type !== 'other') {
            if (g.name) $('#inpGuardianName').val(g.name);
            if (g.relation) $('#inpGuardianRelation').val(g.relation);
            if (g.phone) $('#inpGuardianPhone').val(g.phone);
        } else {
            $('#inpGuardianName').val('').focus();
            $('#inpGuardianRelation').val('');
            $('#inpGuardianPhone').val('');
        }
        syncAllFields();
    }

    // Template Modal Management
    function openTemplateModal() {
        $('#templateSelectionModal').css('display', 'flex');
        $('body').css('overflow', 'hidden');
    }

    function closeTemplateModal() {
        $('#templateSelectionModal').hide();
        $('body').css('overflow', '');
    }

    function selectTemplate(templateId, templateName) {
        $('.template-card').removeClass('active');
        $('#tmpl-card-' + templateId).addClass('active');
        $('#inputTemplate').val(templateId);
        $('#activeTemplateBadge').text(templateId);

        $('.template-view').hide();
        $('#template-' + templateId).fadeIn(150);

        syncAllFields();
        closeTemplateModal();
    }

    // Zoom Controls
    function adjustZoom(delta) {
        currentZoom = Math.min(Math.max(0.5, currentZoom + delta), 1.5);
        applyZoom();
    }

    function resetZoom() {
        currentZoom = 1.0;
        applyZoom();
    }

    function applyZoom() {
        $('#previewContainer').css('transform', `scale(${currentZoom})`);
        $('#zoomLabel').text(Math.round(currentZoom * 100) + '%');
    }

    // Form submission
    function submitGatePassForm(status) {
        $('#inputStatus').val(status || 'issued');
        const form = document.getElementById('gatePassForm');
        if (form.checkValidity()) {
            form.submit();
        } else {
            form.reportValidity();
        }
    }

    // Initial Sync on load
    $(document).ready(function() {
        syncAllFields();
    });
</script>
@endsection
