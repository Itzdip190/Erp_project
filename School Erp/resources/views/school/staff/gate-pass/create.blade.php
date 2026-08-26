@extends('layouts.app')

@section('title', 'Generate Staff Gate Pass — ' . $staff->full_name)
@section('page-title', 'Generate Staff Gate Pass')

@section('styles')
<style>
    /* Split-screen Layout */
    .gatepass-builder-container {
        display: grid;
        grid-template-columns: 460px 1fr;
        gap: 24px;
        align-items: start;
        min-height: calc(100vh - 140px);
    }

    @media (max-width: 1100px) {
        .gatepass-builder-container {
            grid-template-columns: 1fr;
        }
    }

    /* Left Form Column */
    .form-column {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .form-section-title {
        font-size: 13.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--accent);
        margin: 20px 0 12px 0;
        padding-bottom: 6px;
        border-bottom: 1.5px dashed var(--border, #e2e8f0);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title:first-of-type {
        margin-top: 0;
    }

    .field-group {
        margin-bottom: 14px;
    }

    .field-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--t1, #334155);
        margin-bottom: 5px;
    }

    .field-input, .field-select, .field-textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid var(--border, #cbd5e1);
        border-radius: 8px;
        font-size: 13px;
        color: var(--t1, #0f172a);
        background: var(--bg-input, #ffffff);
        transition: all 0.2s ease;
        box-sizing: border-box;
        font-family: inherit;
    }

    .field-input:focus, .field-select:focus, .field-textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.15);
    }

    .preset-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        cursor: pointer;
        border: 1px solid #cbd5e1;
        transition: all 0.15s ease;
        user-select: none;
    }

    .preset-pill:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .preset-pill.active {
        background: var(--accent);
        color: #ffffff;
        border-color: var(--accent);
        box-shadow: 0 2px 6px rgba(var(--accent-rgb), 0.3);
    }

    /* Right Preview Column */
    .preview-column {
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 500px;
        position: sticky;
        top: 20px;
    }

    .preview-toolbar {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border, #e2e8f0);
        flex-wrap: wrap;
        gap: 12px;
    }

    .preview-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--t1, #0f172a);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .preview-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .preview-viewport {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 10px 0;
        overflow-x: auto;
        transition: transform 0.2s ease;
        transform-origin: top center;
    }

    /* Floating Zoom Controls */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border: 1px solid var(--border, #cbd5e1);
        border-radius: 30px;
        padding: 4px 8px;
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
@endsection

@section('content')

<!-- Back Link Breadcrumb -->
<div style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
    <a href="{{ route('school.staff.show', ['staff' => $staff->id, 'tab' => 'gatepass']) }}" class="btn btn-outline" style="padding: 6px 14px; font-size: 12.5px; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
        <i class="fa fa-arrow-left"></i> Back to Staff 360° Profile
    </a>
    <div style="font-size: 12px; color: var(--text-muted);">
        Issuing Gate Pass for: <strong style="color:var(--t1);">{{ $staff->full_name }} ({{ $staff->employee_id }})</strong>
    </div>
</div>

<div class="gatepass-builder-container">
    
    <!-- LEFT: Form Column -->
    <div class="form-column">
        <form id="gatePassForm" action="{{ route('school.staff.gate-passes.store', $staff->id) }}" method="POST">
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

            <!-- Section 2: Staff Details (Auto-filled) -->
            <div class="form-section-title">
                <i class="fa fa-user-tie"></i> Staff Details (Auto-filled)
            </div>

            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px; background:rgba(0,0,0,0.02); padding:10px; border-radius:8px; border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:8px; overflow:hidden; border:1px solid var(--border); flex-shrink:0; background:#f8fafc;">
                    <img src="{{ $staff->photo_url }}" alt="{{ $staff->full_name }}" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div>
                    <strong style="font-size:13.5px; color:var(--t1);">{{ $staff->full_name }}</strong>
                    <div style="font-size:11.5px; color:var(--text-muted);">
                        Emp ID: <strong style="color:var(--accent);">{{ $staff->employee_id }}</strong> &bull; 
                        {{ optional($staff->designation)->name ?? 'Staff' }} ({{ optional($staff->department)->name ?? 'General' }})
                    </div>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Staff Name</label>
                <input type="text" class="field-input live-sync" id="inpStaffName" value="{{ $staff->full_name }}" readonly style="background:#f8fafc;">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                <div class="field-group">
                    <label class="field-label">Employee ID</label>
                    <input type="text" class="field-input" value="{{ $staff->employee_id }}" readonly style="background:#f8fafc;">
                </div>
                <div class="field-group">
                    <label class="field-label">Department</label>
                    <input type="text" class="field-input" value="{{ optional($staff->department)->name ?? 'General' }}" readonly style="background:#f8fafc;">
                </div>
            </div>

            <!-- Section 3: Gate Pass Specifics -->
            <div class="form-section-title">
                <i class="fa fa-ticket-alt"></i> Gate Pass Details
            </div>

            <!-- Reason Selection -->
            <div class="field-group">
                <label class="field-label">Reason for Leaving <span style="color:#ef4444;">*</span></label>
                
                <!-- Quick Preset Pills -->
                <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px;">
                    <span class="preset-pill active" onclick="setReason('Official School Duty', this)">Official Duty</span>
                    <span class="preset-pill" onclick="setReason('Personal Emergency', this)">Personal Emergency</span>
                    <span class="preset-pill" onclick="setReason('Bank / Govt Work', this)">Bank / Govt Work</span>
                    <span class="preset-pill" onclick="setReason('Medical Checkup / Illness', this)">Medical / Illness</span>
                    <span class="preset-pill" onclick="setReason('Early Departure (Approved)', this)">Early Departure</span>
                    <span class="preset-pill" onclick="setReason('Field / Inspection Visit', this)">Field Visit</span>
                    <span class="preset-pill" onclick="setReason('Other', this)">Other</span>
                </div>

                <input type="text" name="reason" id="inpReason" class="field-input live-sync" value="Official School Duty" placeholder="Enter reason for departure" required>
            </div>

            <!-- Date & Time Row -->
            <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:8px;">
                <div class="field-group">
                    <label class="field-label">Current Date & Time</label>
                    <input type="datetime-local" name="pass_date" id="inpPassDate" class="field-input live-sync" value="{{ $currentDateTimeRaw }}">
                </div>
                <div class="field-group">
                    <label class="field-label">Auto Ref No</label>
                    <input type="text" id="inpRefNo" class="field-input" value="{{ $autoNumber }}" readonly style="background:#f8fafc; font-weight:700; color:var(--accent);">
                </div>
            </div>

            <!-- Expected Return Time -->
            <div class="field-group">
                <label class="field-label">Expected Return Time (Optional)</label>
                <input type="text" name="expected_return_time" id="inpReturnTime" class="field-input live-sync" placeholder="e.g. 03:30 PM or Not Returning Today">
            </div>

            <!-- Remarks -->
            <div class="field-group">
                <label class="field-label">Additional Remarks / Note</label>
                <textarea name="remarks" id="inpRemarks" class="field-textarea live-sync" rows="2" placeholder="Optional notes for security..."></textarea>
            </div>

            <!-- Approver -->
            <div class="field-group">
                <label class="field-label">Approved By / Incharge</label>
                <input type="text" name="approved_by" id="inpApprovedBy" class="field-input live-sync" value="{{ $principalName }}">
            </div>

            <!-- Submit Button Bar -->
            <div style="margin-top:24px; padding-top:16px; border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:14px; font-weight:800; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow: 0 4px 14px rgba(var(--accent-rgb), 0.35);">
                    <i class="fa fa-print"></i> Create & Issue Gate Pass
                </button>
            </div>
        </form>
    </div>

    <!-- RIGHT: Live Preview Column -->
    <div class="preview-column">
        
        <!-- Preview Toolbar -->
        <div class="preview-toolbar">
            <div class="preview-title">
                <i class="fa fa-eye" style="color:var(--accent);"></i> Live Real-Time Preview
            </div>

            <div class="preview-actions">
                <button type="button" class="btn btn-outline" style="padding:7px 14px; font-size:12.5px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;" onclick="openTemplateModal()">
                    <i class="fa fa-palette" style="color:var(--accent);"></i> Change Template
                </button>
                <button type="button" class="btn btn-primary" style="padding:7px 16px; font-size:12.5px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;" onclick="document.getElementById('gatePassForm').submit();">
                    <i class="fa fa-check"></i> Create Gatepass
                </button>
            </div>
        </div>

        <!-- Live Viewport Container -->
        <div class="preview-viewport" id="previewViewport">
            <div id="templateContainer" style="width:100%; display:flex; justify-content:center;">
                @if($template === 'modern')
                    @include('school.staff.gate-pass.templates.modern')
                @elseif($template === 'minimal')
                    @include('school.staff.gate-pass.templates.minimal')
                @elseif($template === 'portrait')
                    @include('school.staff.gate-pass.templates.portrait')
                @elseif($template === 'landscape')
                    @include('school.staff.gate-pass.templates.landscape')
                @else
                    @include('school.staff.gate-pass.templates.classic')
                @endif
            </div>
        </div>

        <!-- Zoom Floating Bar -->
        <div class="zoom-controls">
            <button class="zoom-btn" onclick="adjustZoom(-0.1)" title="Zoom Out"><i class="fa fa-minus"></i></button>
            <span id="zoomLevelText" style="font-size:11px; font-weight:700; color:#475569; padding:0 4px;">100%</span>
            <button class="zoom-btn" onclick="adjustZoom(0.1)" title="Zoom In"><i class="fa fa-plus"></i></button>
            <button class="zoom-btn" onclick="resetZoom()" title="Reset" style="font-size:11px;"><i class="fa fa-redo"></i></button>
        </div>
    </div>

</div>

<!-- Template Switcher Modal -->
<div id="templateModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#ffffff; border-radius:16px; max-width:850px; width:90%; padding:24px; box-shadow:0 20px 50px rgba(0,0,0,0.25); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
            <h3 style="margin:0; font-size:17px; font-weight:800; color:#0f172a;">Select Gate Pass Design Template</h3>
            <button onclick="closeTemplateModal()" style="border:none; background:transparent; font-size:18px; color:#64748b; cursor:pointer;"><i class="fa fa-times"></i></button>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
            
            <!-- Classic Template -->
            <div class="template-card {{ $template === 'classic' ? 'active' : '' }}" onclick="selectTemplate('classic', this)">
                <div style="font-weight:800; font-size:14px; margin-bottom:4px; color:#0f172a;">1. Classic Out Pass</div>
                <div style="font-size:11.5px; color:#64748b; line-height:1.4;">Official crest, key-value table format with 3 signature approvals.</div>
            </div>

            <!-- Modern Enterprise -->
            <div class="template-card {{ $template === 'modern' ? 'active' : '' }}" onclick="selectTemplate('modern', this)">
                <div style="font-weight:800; font-size:14px; margin-bottom:4px; color:#0f172a;">2. Modern Enterprise</div>
                <div style="font-size:11.5px; color:#64748b; line-height:1.4;">Navy gradient banner, high contrast cards, modern badges.</div>
            </div>

            <!-- Minimal Template -->
            <div class="template-card {{ $template === 'minimal' ? 'active' : '' }}" onclick="selectTemplate('minimal', this)">
                <div style="font-weight:800; font-size:14px; margin-bottom:4px; color:#0f172a;">3. Minimal Clean</div>
                <div style="font-size:11.5px; color:#64748b; line-height:1.4;">Clean high-contrast borders with clean typography.</div>
            </div>

            <!-- Portrait Slip -->
            <div class="template-card {{ $template === 'portrait' ? 'active' : '' }}" onclick="selectTemplate('portrait', this)">
                <div style="font-weight:800; font-size:14px; margin-bottom:4px; color:#0f172a;">4. Portrait Slip</div>
                <div style="font-size:11.5px; color:#64748b; line-height:1.4;">Compact thermal/half-page format for quick printing.</div>
            </div>

            <!-- Landscape Dual-Copy -->
            <div class="template-card {{ $template === 'landscape' ? 'active' : '' }}" onclick="selectTemplate('landscape', this)">
                <div style="font-weight:800; font-size:14px; margin-bottom:4px; color:#0f172a;">5. Landscape Dual-Copy</div>
                <div style="font-size:11.5px; color:#64748b; line-height:1.4;">Twin counterfoil pass: School Copy + Security Gate Copy.</div>
            </div>
        </div>

        <div style="margin-top:24px; text-align:right; border-top:1px solid #e2e8f0; padding-top:14px;">
            <button class="btn btn-primary" onclick="applySelectedTemplate()" style="padding:8px 22px; font-weight:700; border-radius:8px;">Apply Design</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentZoom = 1;
    let selectedTemplate = '{{ $template }}';

    function setReason(reasonText, element) {
        document.querySelectorAll('.preset-pill').forEach(p => p.classList.remove('active'));
        if (element) element.classList.add('active');
        
        const inp = document.getElementById('inpReason');
        inp.value = reasonText;
        triggerSync();
    }

    function triggerSync() {
        // School Details
        const sName = document.getElementById('inpSchoolName').value;
        const sAddr = document.getElementById('inpSchoolAddress').value;
        const sCity = document.getElementById('inpSchoolCity').value;
        const sState = document.getElementById('inpSchoolState').value;
        const sPin = document.getElementById('inpSchoolPincode').value;

        // Reason & Remarks
        const reason = document.getElementById('inpReason').value;
        const returnTime = document.getElementById('inpReturnTime').value;
        const remarks = document.getElementById('inpRemarks').value;

        // Date
        const dateInput = document.getElementById('inpPassDate').value;
        let formattedDate = '{{ $currentDateTime }}';
        if (dateInput) {
            const d = new Date(dateInput);
            formattedDate = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        }

        // Sync to preview elements
        const updateText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        // Classic template
        updateText('pv-school-name', sName);
        updateText('pv-school-address', sAddr);
        updateText('pv-school-city-state', (sCity ? ', ' + sCity : '') + (sState ? ', ' + sState : ''));
        updateText('pv-school-pincode', sPin);
        updateText('pv-reason', reason);
        updateText('pv-date', formattedDate);
        updateText('pv-return-time', returnTime || '--');

        const pvRemarks = document.getElementById('pv-remarks');
        const pvRowRemarks = document.getElementById('pv-row-remarks');
        if (pvRemarks && pvRowRemarks) {
            pvRemarks.textContent = remarks;
            pvRowRemarks.style.display = remarks.trim() ? '' : 'none';
        }

        // Modern Template
        updateText('pv-school-name-modern', sName);
        updateText('pv-school-address-modern', sAddr);
        updateText('pv-school-city-state-modern', (sCity ? ', ' + sCity : '') + (sState ? ', ' + sState : ''));
        updateText('pv-reason-modern', reason);
        updateText('pv-date-modern', formattedDate);
        updateText('pv-return-time-modern', returnTime || '--');

        // Minimal Template
        updateText('pv-school-name-minimal', sName);
        updateText('pv-school-address-minimal', sAddr);
        updateText('pv-reason-minimal', reason);
        updateText('pv-date-minimal', formattedDate);
        updateText('pv-return-time-minimal', returnTime || '--');

        // Portrait Template
        updateText('pv-school-name-portrait', sName);
        updateText('pv-school-address-portrait', sAddr);
        updateText('pv-reason-portrait', reason);
        updateText('pv-date-portrait', formattedDate);
        updateText('pv-return-time-portrait', returnTime || '--');

        // Landscape Template
        updateText('pv-school-name-land1', sName);
        updateText('pv-school-name-land2', sName);
        updateText('pv-school-address-land1', sAddr);
        updateText('pv-school-address-land2', sAddr);
        updateText('pv-reason-land1', reason);
        updateText('pv-reason-land2', reason);
        updateText('pv-date-land1', formattedDate);
        updateText('pv-date-land2', formattedDate);
    }

    // Bind real-time input listeners
    document.querySelectorAll('.live-sync').forEach(inp => {
        inp.addEventListener('input', triggerSync);
        inp.addEventListener('change', triggerSync);
    });

    // Zoom Controls
    function adjustZoom(delta) {
        currentZoom = Math.min(Math.max(0.6, currentZoom + delta), 1.4);
        applyZoom();
    }

    function resetZoom() {
        currentZoom = 1;
        applyZoom();
    }

    function applyZoom() {
        const viewport = document.getElementById('previewViewport');
        viewport.style.transform = `scale(${currentZoom})`;
        document.getElementById('zoomLevelText').textContent = Math.round(currentZoom * 100) + '%';
    }

    // Template Modal
    function openTemplateModal() {
        document.getElementById('templateModal').style.display = 'flex';
    }

    function closeTemplateModal() {
        document.getElementById('templateModal').style.display = 'none';
    }

    function selectTemplate(tmpl, cardEl) {
        selectedTemplate = tmpl;
        document.querySelectorAll('.template-card').forEach(c => c.classList.remove('active'));
        if (cardEl) cardEl.classList.add('active');
    }

    function applySelectedTemplate() {
        document.getElementById('inputTemplate').value = selectedTemplate;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('template', selectedTemplate);
        window.location.href = currentUrl.toString();
    }

    // Initial Trigger
    document.addEventListener('DOMContentLoaded', () => {
        triggerSync();
    });
</script>
@endsection
