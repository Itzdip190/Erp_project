@extends('layouts.app')

@section('title', 'Add/modify subjects')
@section('page-title', 'Add/modify subjects')

@section('content')
<style>
    /* Styling overhaul for premium feel */
    .filter-bar {
        background: #fff;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .filter-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--t2);
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.5px;
    }
    .filter-select, .filter-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #dcdde1;
        border-radius: 6px;
        font-size: 13px;
        color: var(--t1);
        background: #fcfcfc;
        transition: all 0.3s ease;
    }
    .filter-select:focus, .filter-input:focus {
        border-color: #e06b00;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(224, 107, 0, 0.15);
    }
    
    /* Stats Cards Container */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .stats-card {
        border-radius: 8px;
        padding: 20px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }
    .stats-card::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
        pointer-events: none;
    }
    .stats-card.green {
        background: linear-gradient(135deg, #1b5e20, #2e7d32);
    }
    .stats-card.purple {
        background: linear-gradient(135deg, #4a148c, #6a1b9a);
    }
    .stats-card.indigo {
        background: linear-gradient(135deg, #1a237e, #283593);
    }
    .stats-card-main {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
    }
    .stats-card-lbl {
        font-size: 14px;
        font-weight: 600;
        margin-top: 4px;
        text-transform: capitalize;
        opacity: 0.9;
    }
    .stats-card-sub {
        text-align: right;
        font-size: 12px;
        opacity: 0.85;
        line-height: 1.6;
        font-weight: 500;
    }

    /* Subjects List Table */
    .card-hdr-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .btn-orange-border {
        border: 1px solid #e06b00;
        background: #fff;
        color: #e06b00;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-orange-border:hover {
        background: rgba(224,107,0,0.05);
    }
    .btn-orange-solid {
        background: #e06b00;
        color: #fff;
        border: none;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-orange-solid:hover {
        background: #c95f00;
    }
    
    .tbl th {
        background: #0d353f !important;
        color: #fff !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }
    .tbl td {
        padding: 14px 16px;
        font-size: 13px;
        vertical-align: middle;
    }
    .drag-handle {
        cursor: grab;
        color: var(--t3);
        font-size: 16px;
        margin-right: 8px;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .subject-index {
        color: var(--t3);
        font-weight: 500;
        font-size: 12px;
        margin-right: 4px;
    }
    
    /* Sliding Side Drawer Styling */
    .drawer-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(4px);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    .drawer-backdrop.active {
        opacity: 1;
        visibility: visible;
    }
    .drawer {
        position: fixed;
        top: 0;
        right: -600px;
        width: 580px;
        height: 100vh;
        background: #fff;
        box-shadow: -5px 0 25px rgba(0,0,0,0.15);
        z-index: 1001;
        transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }
    .drawer.active {
        right: 0;
    }
    .drawer-header {
        background: #e06b00;
        color: #fff;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .drawer-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .drawer-subtitle {
        font-size: 12px;
        opacity: 0.85;
        margin-top: 4px;
    }
    .drawer-close {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    .drawer-close:hover {
        opacity: 1;
    }
    .drawer-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }
    .drawer-footer {
        padding: 16px 24px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        background: #fafafa;
    }
    
    /* Stepper Styling */
    .stepper {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 24px;
        position: relative;
    }
    .stepper::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 25%;
        width: 50%;
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 2;
        background: #fff;
        padding: 0 16px;
    }
    .step-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 6px;
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0;
    }
    .step-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--t2);
    }
    .step-item.active .step-circle {
        background: #e06b00;
        color: #fff;
        border-color: #e06b00;
    }
    .step-item.active .step-label {
        color: #e06b00;
    }
    
    /* Custom radio styling matching Orange circles */
    .custom-radio-group {
        display: flex;
        gap: 16px;
        margin-top: 8px;
    }
    .custom-radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--t1);
        cursor: pointer;
    }
    .custom-radio-input {
        display: none;
    }
    .custom-radio-circle {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #dcdde1;
        display: inline-block;
        position: relative;
        transition: all 0.2s ease;
    }
    .custom-radio-input:checked + .custom-radio-circle {
        border-color: #e06b00;
    }
    .custom-radio-input:checked + .custom-radio-circle::after {
        content: '';
        width: 8px;
        height: 8px;
        background: #e06b00;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    /* Grid for Step 2 Classes Selection */
    .classes-selection-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 16px;
    }
    .class-checkbox-card {
        border: 1px solid #dcdde1;
        border-radius: 6px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fbfbfb;
    }
    .class-checkbox-card:hover {
        border-color: #e06b00;
        background: rgba(224, 107, 0, 0.02);
    }
    .class-checkbox-card.selected {
        border-color: #e06b00;
        background: rgba(224, 107, 0, 0.05);
    }

    .btn-edit {
        color: #e06b00;
        background: transparent;
        border: none;
        cursor: pointer;
        margin-right: 12px;
        font-size: 15px;
    }
    .btn-edit:hover {
        color: #c95f00;
    }
    .btn-delete {
        color: #d32f2f;
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 15px;
    }
    .btn-delete:hover {
        color: #b71c1c;
    }

    /* ── Full-row drag styles ─────────────────────────────────────────── */
    #sortableSubjectList tr {
        cursor: grab;
        transition: background 0.15s, box-shadow 0.15s;
    }
    #sortableSubjectList tr:active {
        cursor: grabbing;
    }
    #sortableSubjectList tr.sortable-ghost {
        opacity: 0.45;
        background: #fffbf5 !important;
        box-shadow: inset 3px 0 0 #e06b00;
    }
    #sortableSubjectList tr.sortable-chosen {
        background: #fff8f0 !important;
        box-shadow: 0 4px 16px rgba(224,107,0,0.12);
    }
    /* prevent text selection while dragging */
    #sortableSubjectList.dragging * {
        user-select: none;
    }

    /* ── Save-order floating action bar ──────────────────────────────── */
    .order-save-bar {
        display: none;
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 500;
        background: #1e293b;
        color: #e2e8f0;
        padding: 12px 20px;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.18);
        border-top: 2px solid #e06b00;
        animation: barSlideUp 0.22s ease;
    }
    .order-save-bar.visible {
        display: flex;
    }
    .order-save-bar-msg {
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .order-save-bar-msg i {
        color: #f59e0b;
        font-size: 16px;
    }
    .order-save-bar-actions {
        display: flex;
        gap: 10px;
    }
    .btn-save-order {
        background: #e06b00;
        color: #fff;
        border: none;
        font-size: 12px;
        font-weight: 700;
        padding: 9px 22px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-save-order:hover { background: #c95f00; }
    .btn-cancel-order {
        background: transparent;
        color: #94a3b8;
        border: 1px solid #475569;
        font-size: 12px;
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-cancel-order:hover {
        color: #e2e8f0;
        border-color: #94a3b8;
    }
    @keyframes barSlideUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Top Filters Card -->
<div class="filter-bar">
    <form id="filtersForm" method="GET" action="{{ route('school.assignments.subjects') }}">
        <div class="filter-grid">
            <div>
                <label class="filter-label">Select Class</label>
                <select name="class_id" class="filter-select" onchange="this.form.submit()">
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label">Select Subject Type</label>
                <select name="subject_type" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($subjectTypes as $type)
                        <option value="{{ $type }}" {{ $typeFilter == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label">Select Subject/Course</label>
                <div style="position: relative;">
                    <input type="text" name="search_query" class="filter-input" placeholder="Search code or name..." value="{{ $searchQuery }}">
                    @if($searchQuery)
                        <span style="position: absolute; right: 12px; top: 10px; cursor: pointer; color: var(--t3);" onclick="document.getElementsByName('search_query')[0].value=''; document.getElementById('filtersForm').submit();">&times;</span>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards Dashboard -->
<div class="stats-container">
    <!-- Green Stats Card -->
    <div class="stats-card green">
        <div>
            <div class="stats-card-main">{{ $totalSubjectsCount }}</div>
            <div class="stats-card-lbl">Total Subjects</div>
        </div>
        <div class="stats-card-sub">
            Mandatory: {{ $totalMandatory }}<br>
            Elective: {{ $totalElective }}
        </div>
    </div>
    
    <!-- Purple Stats Card -->
    <div class="stats-card purple">
        <div>
            <div class="stats-card-main">{{ $scholasticCount }}</div>
            <div class="stats-card-lbl">Scholastic</div>
        </div>
        <div class="stats-card-sub">
            Mandatory: {{ $scholasticMandatory }}<br>
            Elective: {{ $scholasticElective }}
        </div>
    </div>
    
    <!-- Indigo/Blue Stats Card -->
    <div class="stats-card indigo">
        <div>
            <div class="stats-card-main">{{ $customCount }}</div>
            <div class="stats-card-lbl">custom subject</div>
        </div>
        <div class="stats-card-sub">
            Mandatory: {{ $customMandatory }}<br>
            Elective: {{ $customElective }}
        </div>
    </div>
</div>

<!-- Subjects List Grid -->
<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>{{ $selectedClass ? $selectedClass->name : 'NUR' }} Subjects list</h3>
        <div class="card-hdr-actions">
            <button type="button" class="btn-orange-border" onclick="openLogsDrawer()"><i class="fas fa-history"></i> SUBJECT LOGS</button>
            <button type="button" class="btn-orange-solid" onclick="openAddSubjectDrawer()"><i class="fas fa-plus"></i> ADD NEW SUBJECT</button>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width: 40%">Subject Name</th>
                        <th style="width: 15%">Subject Code</th>
                        <th style="width: 20%">Description</th>
                        <th style="width: 10%">Type</th>
                        <th style="width: 10%">Mandatory/Elective</th>
                        <th style="width: 5%">Action</th>
                    </tr>
                </thead>
                <tbody id="sortableSubjectList">
                    @forelse($subjects as $index => $sub)
                    <tr data-id="{{ $sub->id }}">
                        <td>
                            <span class="drag-handle" title="Drag entire row to reorder"><i class="fas fa-grip-vertical"></i></span>
                            <span class="subject-index">{{ sprintf('%02d', $index + 1) }}.</span>
                            <strong>{{ $sub->name }}</strong>
                        </td>
                        <td><code style="color:var(--gold); font-weight:700;">{{ $sub->code ?: '—' }}</code></td>
                        <td>{{ $sub->description ?: '—' }}</td>
                        <td>{{ $sub->type }}</td>
                        <td>
                            <span class="badge {{ $sub->is_mandatory ? 'badge-blue' : 'badge-purple' }}">
                                {{ $sub->is_mandatory ? 'Mandatory' : 'Elective' }}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <button type="button" class="btn-edit" onclick="openEditSubjectDrawer({{ json_encode($sub) }})"><i class="fas fa-pencil-alt"></i></button>
                            <form method="POST" action="{{ route('school.assignments.subjects.destroy', $sub->id) }}" onsubmit="return confirm('Are you sure you want to delete this subject?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--t3);">No subjects configured matching filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Save Order Action Bar (sticky at bottom of card) -->
    <div class="order-save-bar" id="orderSaveBar">
        <div class="order-save-bar-msg">
            <i class="fas fa-arrows-alt-v"></i>
            <span>Order changed — save to apply the new sequence.</span>
        </div>
        <div class="order-save-bar-actions">
            <button type="button" class="btn-cancel-order" onclick="cancelReorder()">
                <i class="fas fa-undo"></i> Cancel
            </button>
            <button type="button" class="btn-save-order" id="btnSaveOrder" onclick="saveReorder()">
                <i class="fas fa-save"></i> Save Order
            </button>
        </div>
    </div>
</div>

<!-- Drawer Backdrop overlay -->
<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeAllDrawers()"></div>

<!-- Add/Modify Subject Drawer -->
<div class="drawer" id="subjectDrawer">
    <div class="drawer-header">
        <div>
            <h3 class="drawer-title" id="drawerTitle">Add New Subject</h3>
            <div class="drawer-subtitle" id="drawerSubtitle">Please enter the details below</div>
        </div>
        <button type="button" class="drawer-close" onclick="closeSubjectDrawer()">&times;</button>
    </div>
    
    <!-- Stepper indicator (Only active for Create mode) -->
    <div class="drawer-body">
        <div class="stepper" id="drawerStepper">
            <div class="step-item active" id="step1Indicator">
                <div class="step-circle">1</div>
                <div class="step-label">Subject Details</div>
            </div>
            <div class="step-item" id="step2Indicator">
                <div class="step-circle">2</div>
                <div class="step-label">Select Classes</div>
            </div>
        </div>

        <form id="subjectForm">
            @csrf
            <input type="hidden" id="editSubjectId" name="subject_id">

            <!-- STEP 1: Details -->
            <div id="step1Content">
                <div class="form-group">
                    <label class="form-label">Enter subject name *</label>
                    <input type="text" name="name" id="subjectName" class="filter-input" placeholder="Enter subject name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Enter course code</label>
                    <input type="text" name="code" id="subjectCode" class="filter-input" placeholder="Enter course code">
                </div>
                <div class="form-group">
                    <label class="form-label">Local Name (Optional) <i class="fas fa-info-circle" style="color:var(--t3); cursor:help;" title="Local translation or vernacular script representation of the subject name."></i></label>
                    <input type="text" name="local_name" id="subjectLocalName" class="filter-input" placeholder="Enter local name">
                </div>
                <div class="form-group">
                    <label class="form-label">Enter Description</label>
                    <textarea name="description" id="subjectDescription" class="filter-input" rows="4" placeholder="Enter description..." style="resize:vertical;"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Mandatory/Elective</label>
                    <div class="custom-radio-group">
                        <label class="custom-radio-label">
                            <input type="radio" name="is_mandatory" value="1" class="custom-radio-input" checked id="radioMandatory">
                            <span class="custom-radio-circle"></span>
                            <span>Mandatory</span>
                        </label>
                        <label class="custom-radio-label">
                            <input type="radio" name="is_mandatory" value="0" class="custom-radio-input" id="radioElective">
                            <span class="custom-radio-circle"></span>
                            <span>Elective</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                        <span>Subject Type</span>
                        <button type="button" class="btn-orange-border" style="padding: 2px 8px; font-size: 11px;" onclick="addCustomSubjectType()">+ ADD MORE</button>
                    </label>
                    <div class="custom-radio-group" id="subjectTypeContainer" style="flex-wrap: wrap;">
                        <label class="custom-radio-label">
                            <input type="radio" name="type" value="Scholastic" class="custom-radio-input" checked id="typeScholastic">
                            <span class="custom-radio-circle"></span>
                            <span>Scholastic</span>
                        </label>
                        <label class="custom-radio-label">
                            <input type="radio" name="type" value="Non Scholastic" class="custom-radio-input" id="typeNonScholastic">
                            <span class="custom-radio-circle"></span>
                            <span>Non Scholastic</span>
                        </label>
                        <label class="custom-radio-label">
                            <input type="radio" name="type" value="custom subject" class="custom-radio-input" id="typeCustomSubject">
                            <span class="custom-radio-circle"></span>
                            <span>custom subject <i class="fas fa-pencil-alt" style="margin-left: 2px; font-size: 10px;" onclick="renameCustomType(event)"></i></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Classes Checklist -->
            <div id="step2Content" style="display:none;">
                <h4 style="font-size:14px; margin-bottom: 6px; font-weight: 600;">Choose Target Classes</h4>
                <div style="font-size:12px; color:var(--t3); margin-bottom:12px;">This subject will be added to all selected classes below.</div>
                
                <div class="classes-selection-grid">
                    @foreach($classes as $c)
                    <label class="class-checkbox-card" id="classCard_{{ $c->id }}">
                        <input type="checkbox" name="class_ids[]" value="{{ $c->id }}" onchange="toggleClassCard(this, {{ $c->id }})" {{ $classId == $c->id ? 'checked' : '' }}>
                        <span>{{ $c->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
    
    <div class="drawer-footer">
        <button type="button" class="btn btn-secondary" id="btnBack" style="display:none;" onclick="goToStep1()">Back</button>
        <button type="button" class="btn btn-secondary" id="btnCancel" onclick="closeSubjectDrawer()">Cancel</button>
        <button type="button" class="btn btn-primary" id="btnNext" onclick="goToStep2()">Next &rarr;</button>
        <button type="button" class="btn-orange-solid" id="btnSave" style="display:none;" onclick="saveSubject()">Save Subject</button>
    </div>
</div>

<!-- Logs Drawer -->
<div class="drawer" id="logsDrawer">
    <div class="drawer-header" style="background:#0d353f;">
        <div>
            <h3 class="drawer-title">Subject Logs</h3>
            <div class="drawer-subtitle">Timeline audit trail of recent subject changes</div>
        </div>
        <button type="button" class="drawer-close" onclick="closeLogsDrawer()">&times;</button>
    </div>
    <div class="drawer-body">
        <div id="logsTimeline" style="position:relative; padding-left:24px;">
            <!-- Timeline details will be injected via JS -->
            Loading subject audit logs...
        </div>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-secondary" onclick="closeLogsDrawer()">Close</button>
    </div>
</div>

<!-- Load SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    // Handles classes checkbox visual highlights
    function toggleClassCard(checkbox, cId) {
        const card = document.getElementById('classCard_' + cId);
        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }

    // Initialize checking active selected class cards
    document.querySelectorAll('input[name="class_ids[]"]').forEach(cb => {
        if(cb.checked) {
            const cardId = cb.value;
            const card = document.getElementById('classCard_' + cardId);
            if(card) card.classList.add('selected');
        }
    });

    // Stepper Wizard Navigation
    function goToStep2() {
        const name = document.getElementById('subjectName').value.trim();
        if(!name) {
            alert('Please enter a subject name.');
            document.getElementById('subjectName').focus();
            return;
        }
        
        document.getElementById('step1Content').style.display = 'none';
        document.getElementById('step2Content').style.display = 'block';
        
        document.getElementById('step1Indicator').classList.remove('active');
        document.getElementById('step2Indicator').classList.add('active');
        
        document.getElementById('btnBack').style.display = 'inline-block';
        document.getElementById('btnCancel').style.display = 'none';
        document.getElementById('btnNext').style.display = 'none';
        document.getElementById('btnSave').style.display = 'inline-block';
    }

    function goToStep1() {
        document.getElementById('step1Content').style.display = 'block';
        document.getElementById('step2Content').style.display = 'none';
        
        document.getElementById('step1Indicator').classList.add('active');
        document.getElementById('step2Indicator').classList.remove('active');
        
        document.getElementById('btnBack').style.display = 'none';
        document.getElementById('btnCancel').style.display = 'inline-block';
        document.getElementById('btnNext').style.display = 'inline-block';
        document.getElementById('btnSave').style.display = 'none';
    }

    // Dynamic Custom Subject Types Addition
    function addCustomSubjectType() {
        const newType = prompt("Enter custom subject type:");
        if (newType && newType.trim() !== '') {
            const cleanType = newType.trim();
            const container = document.getElementById('subjectTypeContainer');
            
            // Check if it already exists
            const existingRadios = container.querySelectorAll('input[name="type"]');
            for(let radio of existingRadios) {
                if(radio.value.toLowerCase() === cleanType.toLowerCase()) {
                    radio.checked = true;
                    return;
                }
            }

            const uniqueId = 'typeCustom_' + Date.now();
            const newRadioHtml = `
                <label class="custom-radio-label">
                    <input type="radio" name="type" value="${cleanType}" class="custom-radio-input" checked id="${uniqueId}">
                    <span class="custom-radio-circle"></span>
                    <span>${cleanType}</span>
                </label>
            `;
            container.insertAdjacentHTML('beforeend', newRadioHtml);
        }
    }

    function renameCustomType(event) {
        event.stopPropagation();
        event.preventDefault();
        const input = document.getElementById('typeCustomSubject');
        const span = input.closest('label').querySelector('span:last-child');
        const currentName = input.value;
        const newName = prompt("Rename subject type:", currentName);
        if (newName && newName.trim() !== '') {
            const clean = newName.trim();
            input.value = clean;
            span.innerHTML = `${clean} <i class="fas fa-pencil-alt" style="margin-left: 2px; font-size: 10px;" onclick="renameCustomType(event)"></i>`;
        }
    }

    // Open Drawers
    function openAddSubjectDrawer() {
        // Reset Form
        document.getElementById('subjectForm').reset();
        document.getElementById('editSubjectId').value = '';
        
        // Setup labels
        document.getElementById('drawerTitle').innerText = 'Add New Subject';
        document.getElementById('drawerSubtitle').innerText = 'Please enter the details below';
        
        // Show Stepper & step 1
        document.getElementById('drawerStepper').style.display = 'flex';
        goToStep1();

        document.getElementById('drawerBackdrop').classList.add('active');
        document.getElementById('subjectDrawer').classList.add('active');
    }

    function openEditSubjectDrawer(subject) {
        // Reset form first
        document.getElementById('subjectForm').reset();
        
        document.getElementById('editSubjectId').value = subject.id;
        document.getElementById('subjectName').value = subject.name;
        document.getElementById('subjectCode').value = subject.code;
        document.getElementById('subjectLocalName').value = subject.local_name || '';
        document.getElementById('subjectDescription').value = subject.description || '';

        // Mandatory radio
        if(subject.is_mandatory == 1) {
            document.getElementById('radioMandatory').checked = true;
        } else {
            document.getElementById('radioElective').checked = true;
        }

        // Subject Type
        const container = document.getElementById('subjectTypeContainer');
        const radios = container.querySelectorAll('input[name="type"]');
        let matched = false;
        radios.forEach(radio => {
            if(radio.value === subject.type) {
                radio.checked = true;
                matched = true;
            }
        });

        if(!matched) {
            // Add custom type dynamically
            const uniqueId = 'typeCustom_' + Date.now();
            const newRadioHtml = `
                <label class="custom-radio-label">
                    <input type="radio" name="type" value="${subject.type}" class="custom-radio-input" checked id="${uniqueId}">
                    <span class="custom-radio-circle"></span>
                    <span>${subject.type}</span>
                </label>
            `;
            container.insertAdjacentHTML('beforeend', newRadioHtml);
        }

        // Setup labels for Edit mode (No Step 2 stepper needed since class mapping is managed on creation)
        document.getElementById('drawerTitle').innerText = 'Edit Subject';
        document.getElementById('drawerSubtitle').innerText = 'Modify details for this subject';
        
        document.getElementById('drawerStepper').style.display = 'none';
        document.getElementById('step1Content').style.display = 'block';
        document.getElementById('step2Content').style.display = 'none';
        
        document.getElementById('btnBack').style.display = 'none';
        document.getElementById('btnCancel').style.display = 'inline-block';
        document.getElementById('btnNext').style.display = 'none';
        document.getElementById('btnSave').style.display = 'inline-block';
        document.getElementById('btnSave').innerText = 'Update Subject';

        document.getElementById('drawerBackdrop').classList.add('active');
        document.getElementById('subjectDrawer').classList.add('active');
    }

    function closeSubjectDrawer() {
        document.getElementById('drawerBackdrop').classList.remove('active');
        document.getElementById('subjectDrawer').classList.remove('active');
    }

    // Save/Update Subject AJAX
    function saveSubject() {
        const form = document.getElementById('subjectForm');
        const editId = document.getElementById('editSubjectId').value;
        
        const url = editId 
            ? "{{ route('school.assignments.subjects') }}/" + editId
            : "{{ route('school.assignments.subjects.store') }}";
        
        const method = editId ? 'PUT' : 'POST';
        
        const formData = new FormData(form);
        const object = {};
        formData.forEach((value, key) => {
            if(key === 'class_ids[]') {
                if(!object['class_ids']) object['class_ids'] = [];
                object['class_ids'].push(value);
            } else {
                object[key] = value;
            }
        });

        // Add class_ids if not created by FormData array logic
        if(!editId && !object['class_ids']) {
            object['class_ids'] = [];
            document.querySelectorAll('input[name="class_ids[]"]:checked').forEach(cb => {
                object['class_ids'].push(cb.value);
            });
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(object)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'An error occurred.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while saving the subject.');
        });
    }

    // ── Full-row drag-and-drop with Save / Cancel bar ────────────────────────
    const el = document.getElementById('sortableSubjectList');

    // Snapshot of original row order (DOM nodes) before any drag
    let originalOrder = el ? Array.from(el.querySelectorAll('tr')) : [];
    let pendingOrder  = false;  // true once the user has dragged at least once

    if (el) {
        new Sortable(el, {
            // NO handle → entire row is draggable
            animation : 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass : 'sortable-drag',
            scroll    : true,
            scrollSensitivity: 60,
            scrollSpeed: 12,

            // Ignore clicks on buttons / forms so they still work
            filter: '.btn-edit, .btn-delete, form, button',
            preventOnFilter: false,

            onStart: function() {
                el.classList.add('dragging');
            },

            onEnd: function() {
                el.classList.remove('dragging');

                // Recalculate sequential index labels immediately
                el.querySelectorAll('tr').forEach((tr, i) => {
                    const idx = tr.querySelector('.subject-index');
                    if (idx) idx.innerText = String(i + 1).padStart(2, '0') + '.';
                });

                // Show save bar
                pendingOrder = true;
                document.getElementById('orderSaveBar').classList.add('visible');
            }
        });
    }

    function saveReorder() {
        const btn = document.getElementById('btnSaveOrder');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const orderedIds = [];
        el.querySelectorAll('tr').forEach(tr => { orderedIds.push(tr.dataset.id); });

        fetch("{{ route('school.assignments.subjects.reorder') }}", {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN'  : document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ordered_ids: orderedIds })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Accept new order — update snapshot
                originalOrder = Array.from(el.querySelectorAll('tr'));
                pendingOrder  = false;
                hideOrderBar();
                showOrderToast('✓ Subject order saved successfully.');
            } else {
                alert('Could not save reordering.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Save Order';
            }
        })
        .catch(() => {
            alert('Network error while saving order.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save Order';
        });
    }

    function cancelReorder() {
        if (!pendingOrder) return;

        // Restore original DOM row order
        originalOrder.forEach(tr => el.appendChild(tr));

        // Re-number labels
        el.querySelectorAll('tr').forEach((tr, i) => {
            const idx = tr.querySelector('.subject-index');
            if (idx) idx.innerText = String(i + 1).padStart(2, '0') + '.';
        });

        pendingOrder = false;
        hideOrderBar();
    }

    function hideOrderBar() {
        const bar = document.getElementById('orderSaveBar');
        bar.classList.remove('visible');
        document.getElementById('btnSaveOrder').disabled = false;
        document.getElementById('btnSaveOrder').innerHTML = '<i class="fas fa-save"></i> Save Order';
    }

    function showOrderToast(msg) {
        // Reuse appToast if it exists, else a quick inline alert
        const toast = document.getElementById('appToast');
        if (toast) {
            toast.innerText = msg;
            toast.style.background = '#1e293b';
            toast.style.borderLeft = '3px solid #e06b00';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        } else {
            console.log(msg);
        }
    }

    // Logs Timeline Drawer
    function openLogsDrawer() {
        const container = document.getElementById('logsTimeline');
        container.innerHTML = '<div style="padding:16px; text-align:center;"><i class="fas fa-spinner fa-spin"></i> Loading logs...</div>';
        
        document.getElementById('drawerBackdrop').classList.add('active');
        document.getElementById('logsDrawer').classList.add('active');

        fetch("{{ route('school.assignments.subjects.logs') }}")
        .then(res => res.json())
        .then(data => {
            if(data.success && data.logs.length > 0) {
                let html = '';
                data.logs.forEach(log => {
                    html += `
                        <div style="position:relative; margin-bottom: 20px; border-bottom: 1px solid #f9f9f9; padding-bottom: 12px;">
                            <div style="position:absolute; left:-30px; top:2px; width:12px; height:12px; border-radius:50%; background:#e06b00; border:2px solid #fff; box-shadow:0 0 0 2px rgba(224,107,0,0.2);"></div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <strong style="font-size:13px; color:#0d353f;">${log.row_reference}</strong>
                                <span style="font-size:11px; color:var(--t3);">${log.changed_at}</span>
                            </div>
                            <div style="font-size:12px; color:var(--t2); margin-bottom:2px;">
                                <strong>Action:</strong> ${log.field_changed} (${log.changed_by})
                            </div>
                            ${log.old_value ? `<div style="font-size:11px; color:#d32f2f; background:#fff8f8; padding:4px 8px; border-radius:4px; margin-top:4px;"><strong>Old:</strong> ${log.old_value}</div>` : ''}
                            ${log.new_value ? `<div style="font-size:11px; color:#2e7d32; background:#f4faf4; padding:4px 8px; border-radius:4px; margin-top:4px;"><strong>New:</strong> ${log.new_value}</div>` : ''}
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div style="padding:16px; text-align:center; color:var(--t3);">No subject activity logs available.</div>';
            }
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<div style="padding:16px; text-align:center; color:#d32f2f;">Failed to load logs.</div>';
        });
    }

    function closeLogsDrawer() {
        document.getElementById('drawerBackdrop').classList.remove('active');
        document.getElementById('logsDrawer').classList.remove('active');
    }

    function closeAllDrawers() {
        closeSubjectDrawer();
        closeLogsDrawer();
    }
</script>
@endsection
