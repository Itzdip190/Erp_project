@extends('layouts.app')

@section('title', 'Add/modify class')

@section('styles')
<style>
    /* Stats Row styling */
    .classes-stats-row {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .stat-card-custom {
        display: flex;
        align-items: center;
        border-radius: 6px;
        overflow: hidden;
        height: 72px;
        min-width: 200px;
        box-shadow: var(--shadow);
    }

    .stat-card-custom.green .icon-box {
        background: #0d6e4b;
        color: #fff;
        width: 56px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-card-custom.green .info-box {
        background: #199c68;
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-left: 16px;
        color: #fff;
    }

    .stat-card-custom.red .icon-box {
        background: #911a1a;
        color: #fff;
        width: 56px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-card-custom.red .info-box {
        background: #d32f2f;
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-left: 16px;
        color: #fff;
    }

    .stat-card-custom .stat-number {
        font-size: 20px;
        font-weight: 800;
        line-height: 1.1;
    }

    .stat-card-custom .stat-label {
        font-size: 11px;
        font-weight: 700;
        opacity: 0.9;
    }

    /* Actions Bar */
    .actions-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .btn-action-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        font-size: 11.5px;
        font-weight: 700;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
        height: 38px;
    }

    .btn-action-custom.logs {
        border: 1px solid #c5c5c5;
        background: #fff;
        color: #475569;
    }

    .btn-action-custom.logs:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .btn-action-custom.add {
        background: #d97706;
        color: #fff;
        border: none;
    }

    .btn-action-custom.add:hover {
        background: #b45309;
    }

    .btn-action-custom.reorder {
        border: 1px solid #d97706;
        background: #fff;
        color: #d97706;
    }

    .btn-action-custom.reorder:hover {
        background: rgba(217, 119, 6, 0.05);
    }

    /* Table styling */
    .classes-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    table.tbl-classes {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    table.tbl-classes th {
        background: #0a4b5c;
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        padding: 12px 20px;
        text-align: left;
    }

    table.tbl-classes td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    table.tbl-classes tr:last-child td {
        border-bottom: none;
    }

    .class-num-prefix {
        color: #9ca3af;
        font-weight: 700;
        font-size: 11px;
        margin-right: 8px;
    }

    .class-name-text {
        font-weight: 700;
        color: var(--navy);
    }

    .section-names-text {
        font-weight: 700;
        color: #475569;
    }

    .pencil-edit-icon {
        color: #d97706;
        font-size: 15px;
        cursor: pointer;
        transition: color 0.15s;
    }

    .pencil-edit-icon:hover {
        color: #b45309;
    }

    /* Side Drawer Layout */
    .side-drawer {
        position: fixed;
        top: 0;
        right: -520px;
        width: 500px;
        height: 100vh;
        background: #fff;
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .side-drawer.open {
        right: 0;
    }

    .drawer-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.4);
        z-index: 999;
        display: none;
    }

    .drawer-backdrop.show {
        display: block;
    }

    .drawer-hdr {
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }

    .drawer-hdr.orange-hdr {
        background: #f97316;
    }

    .drawer-hdr.plain-hdr {
        background: #fff;
        color: var(--navy);
        border-bottom: 1px solid var(--border);
    }

    .drawer-hdr h3 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }

    .drawer-close-btn {
        background: none;
        border: none;
        color: inherit;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.85;
        transition: opacity 0.15s;
    }

    .drawer-close-btn:hover {
        opacity: 1;
    }

    .drawer-content {
        flex: 1;
        overflow-y: auto;
        padding: 24px 20px;
    }

    .drawer-footer {
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }

    /* Custom Fieldsets */
    .fieldset-custom {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 12px 8px;
        margin-bottom: 16px;
        position: relative;
        background: #fff;
    }

    .fieldset-custom legend {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        padding: 0 4px;
        margin-left: -4px;
        background: #fff;
        display: inline-block;
        width: auto;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .fieldset-custom input, .fieldset-custom select {
        border: none !important;
        outline: none !important;
        width: 100%;
        font-size: 13.5px;
        color: #1e293b;
        background: transparent;
        padding: 2px 0 0;
        box-shadow: none !important;
        height: auto;
    }

    .fieldset-custom .fieldset-info-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-20%);
        color: #94a3b8;
        cursor: pointer;
    }

    .section-item-box {
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        background: #f8fafc;
        position: relative;
    }

    .delete-section-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ef4444;
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Reorder List */
    .reorder-list-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .reorder-card-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #fff;
        user-select: none;
        transition: background 0.15s, border-color 0.15s;
    }

    .reorder-card-item.over {
        border-color: #f97316;
        background: #fff7ed;
    }

    /* Timeline logs */
    .log-timeline {
        position: relative;
        padding-left: 10px;
    }

    .log-item {
        border-left: 2px solid #cbd5e1;
        padding-left: 18px;
        padding-bottom: 20px;
        position: relative;
    }

    .log-item:last-child {
        border-left: none;
        padding-bottom: 0;
    }

    .log-dot {
        position: absolute;
        left: -6px;
        top: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #f97316;
    }

    .log-meta {
        font-size: 10.5px;
        font-weight: 700;
        color: #64748b;
    }

    .log-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--navy);
        margin: 2px 0 4px;
    }

    .log-desc {
        font-size: 11.5px;
        color: #475569;
        background: #f1f5f9;
        padding: 6px 10px;
        border-radius: 4px;
        font-family: monospace;
        word-break: break-all;
    }
</style>
@endsection

@section('content')
<!-- Page Header Info -->
<div class="page-hdr" style="margin-bottom: 14px;">
    <div class="page-hdr-left">
        <h1>
            Add/modify class
            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; background:rgba(245,158,11,0.2); border-radius:50%; font-size:12px; color:#f59e0b; margin-left:6px;">
                <i class="fas fa-chevron-down"></i>
            </span>
        </h1>
        <p style="color:var(--t2); font-size:12px;">Class, Subject & Teacher Assignment</p>
    </div>
</div>

<!-- Stats Row & Buttons -->
<div class="classes-stats-row">
    <!-- Total Classes Card -->
    <div class="stat-card-custom green">
        <div class="icon-box">
            <i class="fas fa-book"></i>
        </div>
        <div class="info-box">
            <div class="stat-number">{{ sprintf('%02d', $totalClasses) }}</div>
            <div class="stat-label">Total Classes</div>
        </div>
    </div>

    <!-- Total Sections Card -->
    <div class="stat-card-custom red">
        <div class="icon-box">
            <i class="fas fa-book-open"></i>
        </div>
        <div class="info-box">
            <div class="stat-number">{{ sprintf('%02d', $totalSections) }}</div>
            <div class="stat-label">Total Sections</div>
        </div>
    </div>

    <!-- Top Right actions -->
    <div class="actions-bar" style="margin-left: auto; margin-bottom: 0;">
        <button type="button" onclick="openLogsDrawer()" class="btn-action-custom logs">
            <i class="fas fa-history"></i> Class Logs
        </button>
        <button type="button" onclick="openAddClassDrawer()" class="btn-action-custom add">
            <i class="fas fa-plus"></i> Add New Class
        </button>
        <button type="button" onclick="openReorderDrawer()" class="btn-action-custom reorder">
            <i class="fas fa-sort-amount-down-alt"></i> Reorder Classes
        </button>
    </div>
</div>

<!-- Classes Grid Table -->
<div class="classes-card">
    <table class="tbl-classes">
        <thead>
            <tr>
                <th style="width: 250px;">Class Name</th>
                <th>Sections</th>
                <th style="width: 150px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $c)
                <tr>
                    <td>
                        <span class="class-num-prefix">{{ sprintf('%02d.', $loop->iteration) }}</span>
                        <span class="class-name-text">{{ $c->name }}</span>
                    </td>
                    <td>
                        <span class="section-names-text">
                            @php
                                $secNames = [];
                                foreach($c->sections as $sec) {
                                    $secNames[] = $sec->name;
                                }
                                echo implode(', ', $secNames);
                            @endphp
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <i class="fas fa-pencil pencil-edit-icon" onclick="openEditClassDrawer({{ $c->id }})" title="Edit Class"></i>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 32px; color: var(--t3);">
                        No classes configured yet. Click "+ Add New Class" to begin.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Backdrop Drawer Overlay -->
<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeAllDrawers()"></div>

<!-- ══════════ ADD NEW CLASS DRAWER ══════════ -->
<div class="side-drawer" id="addClassDrawer">
    <div class="drawer-hdr orange-hdr">
        <h3>Add New Class</h3>
        <button type="button" class="drawer-close-btn" onclick="closeDrawer('addClassDrawer')"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-content">
        <form id="addClassForm" method="POST" action="{{ route('school.assignments.classes.store') }}">
            @csrf
            <!-- Class Name field -->
            <fieldset class="fieldset-custom">
                <legend>Enter Class Name *</legend>
                <input type="text" name="name" placeholder="Enter Class Name" required>
            </fieldset>

            <!-- Local Name field -->
            <fieldset class="fieldset-custom">
                <legend>Local Name (Optional)</legend>
                <input type="text" name="local_name" placeholder="Enter Class Name">
                <i class="fas fa-info-circle fieldset-info-icon" title="A secondary name for local usage representation."></i>
            </fieldset>

            <!-- Class Code field -->
            <fieldset class="fieldset-custom">
                <legend>Class Code</legend>
                <input type="text" name="class_code" placeholder="Enter Class Code">
            </fieldset>

            <!-- Sections Area -->
            <div style="margin-top: 24px; margin-bottom: 8px;">
                <label style="font-size:12px; font-weight:700; color:var(--navy); text-transform:uppercase;">Sections Setup</label>
            </div>
            
            <div id="addClassSectionsRepeater">
                <div class="section-item-box">
                    <fieldset class="fieldset-custom">
                        <legend>Enter Section Name *</legend>
                        <input type="text" name="sections[0][name]" placeholder="Enter Section Name" required>
                    </fieldset>
                    <fieldset class="fieldset-custom" style="margin-bottom: 0;">
                        <legend>Local Name (Optional)</legend>
                        <input type="text" name="sections[0][local_name]" placeholder="Enter Section Name">
                        <i class="fas fa-info-circle fieldset-info-icon" title="Section translation name."></i>
                    </fieldset>
                </div>
            </div>

            <button type="button" onclick="addNewSectionField('addClassSectionsRepeater')" class="btn btn-outline" style="border-radius:4px; font-weight:700; font-size:11px; padding:6px 12px; border-color:#d97706; color:#d97706; width:100%; display:flex; justify-content:center; gap:6px;">
                <i class="fas fa-plus"></i> ADD SECTIONS
            </button>
        </form>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-outline" style="border-radius:4px;" onclick="closeDrawer('addClassDrawer')">Cancel</button>
        <button type="button" onclick="submitDrawerForm('addClassForm')" class="btn" style="background:#f97316; color:#fff; border-radius:4px; font-weight:700;"><i class="fas fa-save"></i> SAVE</button>
    </div>
</div>

<!-- ══════════ EDIT CLASS DRAWER ══════════ -->
<div class="side-drawer" id="editClassDrawer">
    <div class="drawer-hdr orange-hdr">
        <h3>Edit Class Details</h3>
        <button type="button" class="drawer-close-btn" onclick="closeDrawer('editClassDrawer')"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-content">
        <form id="editClassForm" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Hidden input for class ID -->
            <input type="hidden" id="editClassId" name="class_id">

            <!-- Class Name field -->
            <fieldset class="fieldset-custom">
                <legend>Enter Class Name *</legend>
                <input type="text" id="editClassName" name="name" placeholder="Enter Class Name" required>
            </fieldset>

            <!-- Local Name field -->
            <fieldset class="fieldset-custom">
                <legend>Local Name (Optional)</legend>
                <input type="text" id="editClassLocalName" name="local_name" placeholder="Enter Class Name">
                <i class="fas fa-info-circle fieldset-info-icon" title="A secondary name for local usage representation."></i>
            </fieldset>

            <!-- Class Code field -->
            <fieldset class="fieldset-custom">
                <legend>Class Code</legend>
                <input type="text" id="editClassCode" name="class_code" placeholder="Enter Class Code">
            </fieldset>

            <!-- Sections Setup -->
            <div style="margin-top: 24px; margin-bottom: 8px;">
                <label style="font-size:12px; font-weight:700; color:var(--navy); text-transform:uppercase;">Sections Setup</label>
            </div>
            
            <div id="editClassSectionsRepeater">
                <!-- Dynamically generated list -->
            </div>

            <button type="button" onclick="addNewSectionField('editClassSectionsRepeater')" class="btn btn-outline" style="border-radius:4px; font-weight:700; font-size:11px; padding:6px 12px; border-color:#d97706; color:#d97706; width:100%; display:flex; justify-content:center; gap:6px;">
                <i class="fas fa-plus"></i> ADD SECTIONS
            </button>
        </form>
    </div>
    <div class="drawer-footer" style="justify-content: space-between;">
        <!-- Delete Class action -->
        <button type="button" onclick="triggerDeleteClass()" class="btn btn-danger" style="border-radius:4px; font-weight:700;"><i class="fas fa-trash"></i> DELETE CLASS</button>
        <div style="display:flex; gap:12px;">
            <button type="button" class="btn btn-outline" style="border-radius:4px;" onclick="closeDrawer('editClassDrawer')">Cancel</button>
            <button type="button" onclick="submitDrawerForm('editClassForm')" class="btn" style="background:#f97316; color:#fff; border-radius:4px; font-weight:700;"><i class="fas fa-save"></i> SAVE</button>
        </div>
    </div>
</div>

<!-- ══════════ REORDER CLASSES DRAWER ══════════ -->
<div class="side-drawer" id="reorderDrawer">
    <div class="drawer-hdr plain-hdr">
        <h3>Reorder Classes</h3>
        <button type="button" class="drawer-close-btn" onclick="closeDrawer('reorderDrawer')"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-content">
        <div class="reorder-list-container" id="reorderListContainer">
            @foreach($classes as $c)
                <div class="reorder-card-item" draggable="true" data-id="{{ $c->id }}">
                    <i class="fas fa-grip-vertical" style="color:#94a3b8; cursor:grab; margin-right:4px;"></i>
                    <div style="display:inline-flex; flex-direction:column;">
                        <span style="font-size:12.5px; font-weight:700; color:var(--navy);">
                            {{ $loop->iteration }}. {{ $c->name }}
                        </span>
                        <span style="font-size:10.5px; color:#64748b; font-weight:500;">
                            SECTIONS: 
                            @php
                                $secNames = [];
                                foreach($c->sections as $sec) {
                                    $secNames[] = $sec->name;
                                }
                                echo implode(', ', $secNames);
                            @endphp
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-outline" style="border-radius:4px; border-color:#d97706; color:#d97706; font-weight:700;" onclick="closeDrawer('reorderDrawer')">NO, CANCEL</button>
        <button type="button" onclick="saveReorderSequence()" class="btn" style="background:#cbd5e1; color:#94a3b8; border-radius:4px; font-weight:700;" id="reorderSaveBtn" disabled><i class="fas fa-save"></i> SAVE</button>
    </div>
</div>

<!-- ══════════ CLASS LOGS DRAWER ══════════ -->
<div class="side-drawer" id="logsDrawer">
    <div class="drawer-hdr plain-hdr">
        <h3>Class Logs</h3>
        <button type="button" class="drawer-close-btn" onclick="closeDrawer('logsDrawer')"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-content" id="logsDrawerContent">
        <div class="log-timeline" id="logTimelineContainer">
            <!-- Timeline elements populated via AJAX -->
        </div>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-outline" style="border-radius:4px;" onclick="closeDrawer('logsDrawer')">Close</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Store classes payload globally for fast local updates
    const classesPayload = @json($classes);

    // Open/Close side drawers helper
    function openDrawer(drawerId) {
        closeAllDrawers();
        document.getElementById(drawerId).classList.add('open');
        document.getElementById('drawerBackdrop').classList.add('show');
    }

    function closeDrawer(drawerId) {
        document.getElementById(drawerId).classList.remove('open');
        if (!document.querySelector('.side-drawer.open')) {
            document.getElementById('drawerBackdrop').classList.remove('show');
        }
    }

    function closeAllDrawers() {
        document.querySelectorAll('.side-drawer').forEach(function(drawer) {
            drawer.classList.remove('open');
        });
        document.getElementById('drawerBackdrop').classList.remove('show');
    }

    // Dynamic Section Repeater addition
    let addIndex = 1;
    function addNewSectionField(containerId, secId = '', name = '', localName = '') {
        const index = addIndex++;
        const itemHtml = `
            <div class="section-item-box">
                <button type="button" class="delete-section-btn" onclick="$(this).parent().remove()"><i class="fas fa-times"></i></button>
                <input type="hidden" name="sections[${index}][id]" value="${secId}">
                <fieldset class="fieldset-custom">
                    <legend>Enter Section Name *</legend>
                    <input type="text" name="sections[${index}][name]" value="${name}" placeholder="Enter Section Name" required>
                </fieldset>
                <fieldset class="fieldset-custom" style="margin-bottom: 0;">
                    <legend>Local Name (Optional)</legend>
                    <input type="text" name="sections[${index}][local_name]" value="${localName}" placeholder="Enter Section Name">
                    <i class="fas fa-info-circle fieldset-info-icon" title="Section translation name."></i>
                </fieldset>
            </div>
        `;
        document.getElementById(containerId).insertAdjacentHTML('beforeend', itemHtml);
    }

    // Add Class drawer handlers
    function openAddClassDrawer() {
        document.getElementById('addClassForm').reset();
        document.getElementById('addClassSectionsRepeater').innerHTML = `
            <div class="section-item-box">
                <fieldset class="fieldset-custom">
                    <legend>Enter Section Name *</legend>
                    <input type="text" name="sections[0][name]" placeholder="Enter Section Name" required>
                </fieldset>
                <fieldset class="fieldset-custom" style="margin-bottom: 0;">
                    <legend>Local Name (Optional)</legend>
                    <input type="text" name="sections[0][local_name]" placeholder="Enter Section Name">
                    <i class="fas fa-info-circle fieldset-info-icon" title="Section translation name."></i>
                </fieldset>
            </div>
        `;
        openDrawer('addClassDrawer');
    }

    // Edit Class drawer handlers
    function openEditClassDrawer(classId) {
        const cls = classesPayload.find(item => item.id == classId);
        if (!cls) return;

        document.getElementById('editClassId').value = cls.id;
        document.getElementById('editClassName').value = cls.name;
        document.getElementById('editClassLocalName').value = cls.local_name || '';
        document.getElementById('editClassCode').value = cls.class_code || '';

        // Build sections repeater list
        const container = document.getElementById('editClassSectionsRepeater');
        container.innerHTML = '';

        if (cls.sections && cls.sections.length > 0) {
            cls.sections.forEach(function(sec, index) {
                const itemHtml = `
                    <div class="section-item-box">
                        <button type="button" class="delete-section-btn" onclick="$(this).parent().remove()"><i class="fas fa-times"></i></button>
                        <input type="hidden" name="sections[${index}][id]" value="${sec.id}">
                        <fieldset class="fieldset-custom">
                            <legend>Enter Section Name *</legend>
                            <input type="text" name="sections[${index}][name]" value="${sec.name}" placeholder="Enter Section Name" required>
                        </fieldset>
                        <fieldset class="fieldset-custom" style="margin-bottom: 0;">
                            <legend>Local Name (Optional)</legend>
                            <input type="text" name="sections[${index}][local_name]" value="${sec.local_name || ''}" placeholder="Enter Section Name">
                            <i class="fas fa-info-circle fieldset-info-icon" title="Section translation name."></i>
                        </fieldset>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });
        } else {
            // Default blank section
            addNewSectionField('editClassSectionsRepeater');
        }

        // Set action url
        document.getElementById('editClassForm').action = '/school/assignments/classes/' + classId;
        openDrawer('editClassDrawer');
    }

    // Delete Class triggers
    function triggerDeleteClass() {
        const classId = document.getElementById('editClassId').value;
        if (!classId) return;

        if (confirm('Warning: Deleting this class will delete all its sections and subjects! Proceed?')) {
            let deleteForm = document.createElement('form');
            deleteForm.method = 'POST';
            deleteForm.action = '/school/assignments/classes/' + classId;
            deleteForm.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(deleteForm);
            deleteForm.submit();
        }
    }

    // Handle AJAX Form submits inside Drawers
    function submitDrawerForm(formId) {
        const form = document.getElementById(formId);
        if (!form.reportValidity()) return;

        const actionUrl = form.action;
        const formData = $(form).serialize();

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    closeAllDrawers();
                    showToast(response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('Failed to save class details', true);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to submit data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, true);
            }
        });
    }

    // Open logs drawer and fetch class log timelines
    function openLogsDrawer() {
        const container = document.getElementById('logTimelineContainer');
        container.innerHTML = '<div style="text-align:center; padding:20px; color:#64748b;"><i class="fas fa-spinner fa-spin"></i> Loading Logs...</div>';
        
        openDrawer('logsDrawer');

        $.ajax({
            url: '{{ route("school.assignments.classes.logs") }}',
            type: 'GET',
            success: function(response) {
                if (response.success && response.logs.length > 0) {
                    container.innerHTML = '';
                    response.logs.forEach(function(l) {
                        const itemHtml = `
                            <div class="log-item">
                                <div class="log-dot"></div>
                                <div class="log-meta">${l.changed_at} - ${l.changed_by}</div>
                                <div class="log-title">${l.row_reference} (${l.field_changed})</div>
                                <div class="log-desc">
                                    <strong>Before:</strong> ${l.old_value || '—'}<br>
                                    <strong>After:</strong> ${l.new_value || '—'}
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    container.innerHTML = '<div style="text-align:center; padding:20px; color:#64748b;">No recent modification logs found.</div>';
                }
            },
            error: function() {
                container.innerHTML = '<div style="text-align:center; padding:20px; color:#ef4444;">Failed to load modification logs.</div>';
            }
        });
    }

    // Drag-and-drop sortable implementation for Reorder Classes
    function openReorderDrawer() {
        openDrawer('reorderDrawer');
    }

    let draggedItem = null;

    $(document).on('dragstart', '.reorder-card-item', function(e) {
        draggedItem = this;
        $(this).addClass('dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
    });

    $(document).on('dragend', '.reorder-card-item', function() {
        $(this).removeClass('dragging');
        // Enable Save button
        $('#reorderSaveBtn')
            .removeAttr('disabled')
            .css({
                'background': '#f97316',
                'color': '#fff'
            });
    });

    $(document).on('dragover', '.reorder-list-container', function(e) {
        e.preventDefault();
        const container = $(this);
        const afterElement = getDragAfterElement(container, e.originalEvent.clientY);
        if (afterElement == null) {
            container.append(draggedItem);
        } else {
            $(draggedItem).insertBefore(afterElement);
        }
    });

    $(document).on('dragenter', '.reorder-card-item', function() {
        $(this).addClass('over');
    });

    $(document).on('dragleave', '.reorder-card-item', function() {
        $(this).removeClass('over');
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.find('.reorder-card-item:not(.dragging)')];
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function saveReorderSequence() {
        const orderedIds = [];
        document.querySelectorAll('#reorderListContainer .reorder-card-item').forEach(function(item) {
            orderedIds.push(item.getAttribute('data-id'));
        });

        $.ajax({
            url: '{{ route("school.assignments.classes.reorder") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ordered_ids: orderedIds
            },
            success: function(response) {
                if (response.success) {
                    closeAllDrawers();
                    showToast(response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('Failed to save reorder sequence', true);
                }
            },
            error: function() {
                showToast('Connection error. Failed to save sequence.', true);
            }
        });
    }
</script>
@endsection
