@extends('layouts.app')

@section('title', 'Assign teachers')
@section('page-title', 'Assign teachers')

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
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: flex-end;
    }
    .filter-item {
        flex: 1;
        min-width: 200px;
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
    
    .btn-orange-border {
        border: 1px solid #e06b00;
        background: #fff;
        color: #e06b00;
        font-size: 12px;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-orange-solid:hover {
        background: #c95f00;
    }

    /* Class leaders card */
    .leaders-card {
        background: #fff;
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .leaders-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 220px;
        flex: 1;
    }

    /* Stats Cards Container */
    .stats-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        gap: 20px;
        flex-wrap: wrap;
    }
    .stats-group {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .stats-card-mini {
        border-radius: 6px;
        padding: 12px 20px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 250px;
    }
    .stats-card-mini.green {
        background: #1b5e20;
    }
    .stats-card-mini.red {
        background: #d32f2f;
    }
    .stats-card-icon {
        font-size: 24px;
        opacity: 0.8;
    }
    .stats-card-val {
        font-size: 20px;
        font-weight: 800;
        line-height: 1.1;
    }
    .stats-card-txt {
        font-size: 11px;
        font-weight: 500;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Table Grid Styling */
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
    
    /* Dynamic teacher listing inside cells */
    .teacher-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8f9fa;
        border-radius: 6px;
        padding: 6px 12px;
        margin-bottom: 6px;
        border: 1px solid #e9ecef;
    }
    .teacher-item-name {
        font-weight: 600;
        color: var(--t1);
    }
    .teacher-item-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-action-icon {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 2px;
        font-size: 13px;
        transition: color 0.2s ease;
    }
    .btn-action-icon.edit {
        color: #e06b00;
    }
    .btn-action-icon.edit:hover {
        color: #c95f00;
    }
    .btn-action-icon.delete {
        color: #d32f2f;
    }
    .btn-action-icon.delete:hover {
        color: #b71c1c;
    }

    /* Drawers */
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

    /* Segmented Controls / Tabs */
    .tab-container {
        display: flex;
        border: 1px solid #dcdde1;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .tab-btn {
        flex: 1;
        padding: 10px;
        border: none;
        background: #f5f6fa;
        font-weight: 600;
        font-size: 13px;
        color: var(--t2);
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }
    .tab-btn.active {
        background: #b87000;
        color: #fff;
    }

    /* Multi-select section chips styling */
    .section-chip {
        display: inline-flex;
        align-items: center;
        background: #e1f5fe;
        border: 1px solid #b3e5fc;
        color: #0288d1;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        margin: 4px;
        gap: 6px;
    }
    .section-chip-close {
        cursor: pointer;
        color: #d32f2f;
        font-size: 14px;
    }
    .section-chip-close:hover {
        color: #b71c1c;
    }

    /* Upload box styling */
    .upload-box {
        border: 2px dashed #dcdde1;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }
    .upload-box:hover {
        border-color: #e06b00;
        background: rgba(224, 107, 0, 0.02);
    }
    .upload-box-icon {
        font-size: 40px;
        color: var(--t3);
        margin-bottom: 12px;
    }
</style>

<!-- Top Filter Toolbar -->
<div class="filter-bar">
    <div class="filter-grid">
        <div class="filter-item">
            <label class="filter-label">Academic Year *</label>
            <select id="selectAcademicYear" class="filter-select" onchange="loadGridData()">
                @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ $sessionId == $session->id ? 'selected' : '' }}>
                        {{ $session->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label class="filter-label">Select Class</label>
            <select id="selectClass" class="filter-select" onchange="updateSectionDropdown()">
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label class="filter-label">Select Section</label>
            <select id="selectSection" class="filter-select" onchange="loadGridData()">
                <!-- Populated via Javascript -->
            </select>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="button" class="btn-orange-border" onclick="openLogsDrawer()"><i class="fas fa-history"></i> TEACHER ASSIGNMENT LOGS</button>
            <button type="button" class="btn-orange-border" onclick="openBulkUploadDrawer()"><i class="fas fa-file-upload"></i> BULK UPLOAD MAPPING</button>
        </div>
    </div>
</div>


<!-- Grid Stats Cards & Save Toolbar -->
<div class="stats-container">
    <div class="stats-group">
        <!-- Assigned subjects stats -->
        <div class="stats-card-mini green">
            <div class="stats-card-icon"><i class="fas fa-book-reader"></i></div>
            <div>
                <div class="stats-card-val" id="assignedSubjectsCount">0</div>
                <div class="stats-card-txt">Total subjects with assigned teacher</div>
            </div>
        </div>
        
        <!-- Unassigned subjects stats -->
        <div class="stats-card-mini red">
            <div class="stats-card-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <div class="stats-card-val" id="unassignedSubjectsCount">0</div>
                <div class="stats-card-txt">Total subjects with un-assigned teacher</div>
            </div>
        </div>
    </div>

    <div>
        <button type="button" class="btn-orange-solid" style="padding: 12px 28px; font-size: 14px; background:#b87000;" onclick="saveGridData()"><i class="fas fa-save"></i> SAVE ASSIGNMENTS</button>
    </div>
</div>

<!-- Table Assignment Grid -->
<div class="card">
    <div class="card-hdr">
        <h3 id="tableGridTitle">Subjects & Teacher Assignments</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width: 30%">Subject Name</th>
                        <th style="width: 15%">Subject Code</th>
                        <th style="width: 25%">Primary Teacher</th>
                        <th style="width: 30%">Substitute Teacher</th>
                    </tr>
                </thead>
                <tbody id="teacherGridBody">
                    <tr>
                        <td colspan="4" style="text-align:center; padding:32px; color:var(--t3);"><i class="fas fa-spinner fa-spin"></i> Loading assignment grid...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Backdrop -->
<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeAllDrawers()"></div>

<!-- Bulk Upload Drawer -->
<div class="drawer" id="bulkUploadDrawer">
    <div class="drawer-header">
        <div>
            <h3 class="drawer-title">Subject Teacher Mapping</h3>
            <div class="drawer-subtitle">Bulk Upload</div>
        </div>
        <button type="button" class="drawer-close" onclick="closeBulkUploadDrawer()">&times;</button>
    </div>
    <div class="drawer-body">
        <!-- Segmented Tab selector -->
        <div class="tab-container">
            <button class="tab-btn active" id="tabClassWise" onclick="switchBulkTab('class-wise')">CLASS-WISE</button>
            <button class="tab-btn" id="tabTeacherWise" onclick="switchBulkTab('teacher-wise')">TEACHER-WISE</button>
        </div>

        <!-- Class Wise tab contents -->
        <div id="tabContentClassWise">
            <h4 style="font-size:14px; font-weight:700; margin-bottom:8px;">Class-wise Template</h4>
            <div class="leaders-card" style="padding:12px; margin-bottom:12px; flex-direction:column; align-items:stretch; gap:12px; border:1px solid #dcdde1; box-shadow:none;">
                <div style="display:flex; gap:12px;">
                    <div style="flex:1;">
                        <label class="filter-label">Select Class</label>
                        <select id="bulkClassSelect" class="filter-select" onchange="updateBulkSectionsDropdown()">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label class="filter-label">Select Section</label>
                        <select id="bulkSectionSelect" class="filter-select">
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                </div>
                <div style="text-align:right;">
                    <button type="button" class="btn-orange-border" onclick="addBulkSectionChip()"><i class="fas fa-plus"></i> ADD</button>
                </div>
            </div>

            <!-- Chips container -->
            <label class="filter-label">Selected Sections</label>
            <div class="leaders-card" style="padding:10px; margin-bottom:16px; min-height:80px; align-items:flex-start; justify-content:flex-start; border:1px solid #dcdde1; box-shadow:none; gap:6px;" id="selectedSectionsContainer">
                <!-- Chips injected via JS -->
            </div>
            <div style="text-align:right; margin-bottom:20px;">
                <button type="button" style="border:none; background:transparent; color:#d32f2f; font-weight:600; font-size:11px; cursor:pointer;" onclick="clearAllBulkSections()">CLEAR ALL</button>
            </div>

            <div style="text-align:center; margin-bottom:30px;">
                <button type="button" class="btn-orange-solid" style="background:#b87000; width:100%;" onclick="downloadBulkTemplate()"><i class="fas fa-download"></i> DOWNLOAD CLASS-WISE TEMPLATE</button>
            </div>
        </div>

        <!-- Teacher Wise stub (since mockups emphasize Class-Wise templates upload) -->
        <div id="tabContentTeacherWise" style="display:none;">
            <div style="padding:20px; text-align:center; color:var(--t3); border:1px solid #dcdde1; border-radius:6px;">
                Teacher-wise allocation mapping is available in the Class-wise template sheet.
            </div>
        </div>

        <!-- File Upload Box -->
        <h4 style="font-size:14px; font-weight:700; margin-bottom:8px; border-top:1px solid #eee; padding-top:20px;">Upload Filled Template</h4>
        <div class="upload-box" onclick="document.getElementById('csvFileInput').click()">
            <div class="upload-box-icon"><i class="fas fa-cloud-upload-alt"></i></div>
            <div style="font-size:14px; font-weight:600; color:var(--t1);" id="uploadFileName">CHOOSE FILE</div>
            <div style="font-size:12px; color:var(--t3); margin-top:4px;">Drag and drop or browse to upload CSV</div>
            <input type="file" id="csvFileInput" style="display:none;" accept=".csv" onchange="handleFileSelect(event)">
        </div>

        <div style="font-size:11px; color:var(--t3); margin-top:12px; line-height:1.5;">
            Use employee ID or "Name (EmployeeID)" format for teacher values. Multiple subject teachers should stay comma separated.<br>
            Substitute teachers in the last column are also mapped.
        </div>

        <!-- Import Error Box -->
        <div id="importErrorBox" style="display:none; margin-top:16px; padding:12px; background:#fff8f8; border:1px solid #ffcccc; border-radius:6px; color:#d32f2f; font-size:12px; max-height:150px; overflow-y:auto;">
            <!-- Errors injected via JS -->
        </div>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-secondary" onclick="closeBulkUploadDrawer()">Cancel</button>
        <button type="button" class="btn-orange-solid" id="btnUploadSubmit" onclick="submitBulkMappingFile()"><i class="fas fa-upload"></i> UPLOAD FILE</button>
    </div>
</div>

<!-- Logs Drawer -->
<div class="drawer" id="logsDrawer">
    <div class="drawer-header" style="background:#0d353f;">
        <div>
            <h3 class="drawer-title">Teacher Assignment Logs</h3>
            <div class="drawer-subtitle">Timeline audit trail of recent assignment changes</div>
        </div>
        <button type="button" class="drawer-close" onclick="closeLogsDrawer()">&times;</button>
    </div>
    <div class="drawer-body">
        <div id="logsTimeline" style="position:relative; padding-left:24px;">
            Loading teacher assignment audit logs...
        </div>
    </div>
    <div class="drawer-footer">
        <button type="button" class="btn btn-secondary" onclick="closeLogsDrawer()">Close</button>
    </div>
</div>

<script>
    // In-memory data store for sections mapping & grid state
    const classesData = {!! json_encode($classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'sections' => $c->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name])])) !!};
    const teachersList = {!! json_encode($teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->full_name])) !!};

    let currentClassId = "{{ $classId }}";
    let currentSectionId = null;
    
    // Grid Assignments Cache (Subject -> list of assigned staff objects)
    let gridSubjectAssignments = {}; 

    // Selected Bulk upload sections chips
    let bulkSelectedSections = [];

    // Trigger on document load
    document.addEventListener("DOMContentLoaded", () => {
        updateSectionDropdown();
        updateBulkSectionsDropdown();
    });

    function updateSectionDropdown() {
        const cId = document.getElementById('selectClass').value;
        currentClassId = cId;
        const selectSec = document.getElementById('selectSection');
        selectSec.innerHTML = '';
        
        const matchedClass = classesData.find(c => String(c.id) === String(cId));
        if (matchedClass && matchedClass.sections.length > 0) {
            matchedClass.sections.forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec.id;
                opt.innerText = sec.name;
                selectSec.appendChild(opt);
            });
            currentSectionId = matchedClass.sections[0].id;
        } else {
            const opt = document.createElement('option');
            opt.value = "";
            opt.innerText = "No Sections";
            selectSec.appendChild(opt);
            currentSectionId = null;
        }

        loadGridData();
    }

    function updateBulkSectionsDropdown() {
        const cId = document.getElementById('bulkClassSelect').value;
        const selectSec = document.getElementById('bulkSectionSelect');
        selectSec.innerHTML = '';
        
        const matchedClass = classesData.find(c => String(c.id) === String(cId));
        if (matchedClass && matchedClass.sections.length > 0) {
            matchedClass.sections.forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec.id;
                opt.innerText = sec.name;
                selectSec.appendChild(opt);
            });
        }
    }

    // Load Grid Data via AJAX
    function loadGridData() {
        const sessionId = document.getElementById('selectAcademicYear').value;
        const classId = document.getElementById('selectClass').value;
        const sectionId = document.getElementById('selectSection').value;

        if(!sessionId || !classId || !sectionId) {
            document.getElementById('teacherGridBody').innerHTML = `
                <tr>
                    <td colspan="4" style="text-align:center; padding:32px; color:var(--t3);">Please select filters to display the grid.</td>
                </tr>
            `;
            return;
        }

        const classLabel = document.getElementById('selectClass').options[document.getElementById('selectClass').selectedIndex].text;
        const sectionLabel = document.getElementById('selectSection').options[document.getElementById('selectSection').selectedIndex].text;
        document.getElementById('tableGridTitle').innerText = `${classLabel} - ${sectionLabel} Subjects & Teacher Assignments`;

        fetch(`{{ route('school.assignments.teachers.load-grid') }}?academic_session_id=${sessionId}&class_id=${classId}&section_id=${sectionId}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Populate stats counters
                updateStatsCounters(data.assigned_count, data.unassigned_count);

                // Build Table Rows
                gridSubjectAssignments = {};
                const body = document.getElementById('teacherGridBody');
                body.innerHTML = '';

                data.grid.forEach(row => {
                    gridSubjectAssignments[row.subject_id] = row.assignments || [];
                    
                    const tr = document.createElement('tr');
                    tr.dataset.subjectId = row.subject_id;
                    
                    // Column 1: Subject Name (prefix sequential order)
                    const tdName = document.createElement('td');
                    tdName.innerHTML = `<span style="color:var(--t3); font-weight:500;">${String(row.index).padStart(2, '0')}.</span> <strong>${row.subject_name}</strong>`;
                    
                    // Column 2: Code
                    const tdCode = document.createElement('td');
                    tdCode.innerHTML = `<code style="color:var(--gold); font-weight:700;">${row.subject_code || '—'}</code>`;

                    // Column 3: Primary Teachers Container
                    const tdPrimary = document.createElement('td');
                    tdPrimary.id = `primary_container_${row.subject_id}`;
                    
                    // Column 4: Substitute Teachers Container
                    const tdSubstitute = document.createElement('td');
                    tdSubstitute.id = `substitute_container_${row.subject_id}`;

                    tr.appendChild(tdName);
                    tr.appendChild(tdCode);
                    tr.appendChild(tdPrimary);
                    tr.appendChild(tdSubstitute);
                    body.appendChild(tr);

                    // Render primary + substitute list inside cells
                    renderRowAssignments(row.subject_id);
                });
            } else {
                alert('Failed to load assignments grid.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error loading teacher grid.');
        });
    }

    function renderRowAssignments(subjectId) {
        const primContainer = document.getElementById(`primary_container_${subjectId}`);
        const subContainer = document.getElementById(`substitute_container_${subjectId}`);
        
        primContainer.innerHTML = '';
        subContainer.innerHTML = '';

        const currentList = gridSubjectAssignments[subjectId] || [];

        currentList.forEach((assign, index) => {
            // Render Primary Card
            const primDiv = document.createElement('div');
            primDiv.className = 'teacher-item-row';
            primDiv.innerHTML = `
                <span class="teacher-item-name">${assign.staff_name}</span>
                <div class="teacher-item-actions">
                    <button type="button" class="btn-action-icon delete" onclick="deletePrimaryTeacher(${subjectId}, ${assign.staff_id})"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            primContainer.appendChild(primDiv);

            // Render Substitute Dropdown
            const subDiv = document.createElement('div');
            subDiv.style.marginBottom = '6px';
            
            let optionsHtml = '<option value="">No Teacher Selected</option>';
            teachersList.forEach(t => {
                optionsHtml += `<option value="${t.id}" ${String(t.id) === String(assign.substitute_staff_id) ? 'selected' : ''}>${t.name}</option>`;
            });

            subDiv.innerHTML = `
                <select class="filter-select" style="padding:6px 12px; font-size:12px;" onchange="updateSubstituteTeacher(${subjectId}, ${assign.staff_id}, this.value)">
                    ${optionsHtml}
                </select>
            `;
            subContainer.appendChild(subDiv);
        });

        // Add "Select Staff" dropdown at the bottom of Primary Teacher cell to map new teachers
        const addDiv = document.createElement('div');
        addDiv.style.marginTop = '8px';
        
        // Filter out already assigned teachers to avoid assigning the same teacher twice to the same subject
        const assignedIds = currentList.map(a => String(a.staff_id));
        let selectHtml = '<option value="">Select Staff</option>';
        teachersList.forEach(t => {
            if(!assignedIds.includes(String(t.id))) {
                selectHtml += `<option value="${t.id}">${t.name}</option>`;
            }
        });

        addDiv.innerHTML = `
            <select class="filter-select" style="padding:6px 12px; font-size:12px; border-color:#e06b00; background:#fffbf7;" onchange="addPrimaryTeacher(${subjectId}, this.value)">
                ${selectHtml}
            </select>
        `;
        primContainer.appendChild(addDiv);

        // Render empty placeholder for substitute aligned with the add button
        const emptySubDiv = document.createElement('div');
        emptySubDiv.style.height = '32px';
        emptySubDiv.style.marginTop = '8px';
        subContainer.appendChild(emptySubDiv);
    }

    // In-memory assignment modifications
    function addPrimaryTeacher(subjectId, staffId) {
        if(!staffId) return;

        const staff = teachersList.find(t => String(t.id) === String(staffId));
        if(!staff) return;

        if(!gridSubjectAssignments[subjectId]) {
            gridSubjectAssignments[subjectId] = [];
        }

        gridSubjectAssignments[subjectId].push({
            staff_id: staff.id,
            staff_name: staff.name,
            substitute_staff_id: null,
            substitute_name: 'No Teacher Selected'
        });

        renderRowAssignments(subjectId);
        recalculateUnassignedStats();
    }

    function deletePrimaryTeacher(subjectId, staffId) {
        if(!gridSubjectAssignments[subjectId]) return;

        gridSubjectAssignments[subjectId] = gridSubjectAssignments[subjectId].filter(a => String(a.staff_id) !== String(staffId));
        
        renderRowAssignments(subjectId);
        recalculateUnassignedStats();
    }

    function updateSubstituteTeacher(subjectId, staffId, substituteStaffId) {
        if(!gridSubjectAssignments[subjectId]) return;

        const assign = gridSubjectAssignments[subjectId].find(a => String(a.staff_id) === String(staffId));
        if(assign) {
            assign.substitute_staff_id = substituteStaffId || null;
            if(substituteStaffId) {
                const staff = teachersList.find(t => String(t.id) === String(substituteStaffId));
                assign.substitute_name = staff ? staff.name : 'No Teacher Selected';
            } else {
                assign.substitute_name = 'No Teacher Selected';
            }
        }
    }

    // UI Stats Live calculation
    function recalculateUnassignedStats() {
        let assigned = 0;
        let unassigned = 0;

        Object.keys(gridSubjectAssignments).forEach(subId => {
            if(gridSubjectAssignments[subId].length > 0) {
                assigned++;
            } else {
                unassigned++;
            }
        });

        updateStatsCounters(assigned, unassigned);
    }

    function updateStatsCounters(assigned, unassigned) {
        document.getElementById('assignedSubjectsCount').innerText = assigned;
        document.getElementById('unassignedSubjectsCount').innerText = unassigned;
    }

    // Save grid data via single AJAX payload
    function saveGridData() {
        const sessionId = document.getElementById('selectAcademicYear').value;
        const classId = document.getElementById('selectClass').value;
        const sectionId = document.getElementById('selectSection').value;

        if(!sessionId || !classId || !sectionId) {
            alert('Filter values are incomplete.');
            return;
        }

        // Gather all assignments packaged
        const payloadAssignments = [];
        Object.keys(gridSubjectAssignments).forEach(subjectId => {
            const list = gridSubjectAssignments[subjectId] || [];
            list.forEach(assign => {
                payloadAssignments.push({
                    subject_id: subjectId,
                    staff_id: assign.staff_id,
                    substitute_staff_id: assign.substitute_staff_id
                });
            });
        });

        const classTeacherId = document.getElementById('classTeacherSelect').value;
        const assistantClassTeacherId = document.getElementById('assistantClassTeacherSelect').value;

        const object = {
            academic_session_id: sessionId,
            class_id: classId,
            section_id: sectionId,
            class_teacher_id: classTeacherId || null,
            assistant_class_teacher_id: assistantClassTeacherId || null,
            assignments: payloadAssignments
        };

        fetch("{{ route('school.assignments.teachers.save-grid') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(object)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Assignments saved successfully.');
                loadGridData();
            } else {
                alert('Save failed: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error occurred while saving assignments.');
        });
    }

    // Dedicated save for Leaders dropdowns
    function saveClassLeaders() {
        saveGridData();
    }

    // Bulk upload drawer management
    function openBulkUploadDrawer() {
        bulkSelectedSections = [];
        renderBulkSectionsChips();
        document.getElementById('drawerBackdrop').classList.add('active');
        document.getElementById('bulkUploadDrawer').classList.add('active');
    }

    function closeBulkUploadDrawer() {
        document.getElementById('drawerBackdrop').classList.remove('active');
        document.getElementById('bulkUploadDrawer').classList.remove('active');
        document.getElementById('importErrorBox').style.display = 'none';
        document.getElementById('importErrorBox').innerHTML = '';
        document.getElementById('uploadFileName').innerText = 'CHOOSE FILE';
        document.getElementById('csvFileInput').value = '';
    }

    function switchBulkTab(tab) {
        if(tab === 'class-wise') {
            document.getElementById('tabClassWise').classList.add('active');
            document.getElementById('tabTeacherWise').classList.remove('active');
            document.getElementById('tabContentClassWise').style.display = 'block';
            document.getElementById('tabContentTeacherWise').style.display = 'none';
        } else {
            document.getElementById('tabClassWise').classList.remove('active');
            document.getElementById('tabTeacherWise').classList.add('active');
            document.getElementById('tabContentClassWise').style.display = 'none';
            document.getElementById('tabContentTeacherWise').style.display = 'block';
        }
    }

    function addBulkSectionChip() {
        const cId = document.getElementById('bulkClassSelect').value;
        const sId = document.getElementById('bulkSectionSelect').value;
        
        if(!cId || !sId) return;

        const classLabel = document.getElementById('bulkClassSelect').options[document.getElementById('bulkClassSelect').selectedIndex].text;
        const sectionLabel = document.getElementById('bulkSectionSelect').options[document.getElementById('bulkSectionSelect').selectedIndex].text;

        // Check duplicates
        if(bulkSelectedSections.find(item => String(item.section_id) === String(sId))) {
            return;
        }

        bulkSelectedSections.push({
            section_id: sId,
            label: `${classLabel}-${sectionLabel}`
        });

        renderBulkSectionsChips();
    }

    function removeBulkSectionChip(sId) {
        bulkSelectedSections = bulkSelectedSections.filter(item => String(item.section_id) !== String(sId));
        renderBulkSectionsChips();
    }

    function clearAllBulkSections() {
        bulkSelectedSections = [];
        renderBulkSectionsChips();
    }

    function renderBulkSectionsChips() {
        const container = document.getElementById('selectedSectionsContainer');
        container.innerHTML = '';
        
        if(bulkSelectedSections.length === 0) {
            container.innerHTML = '<span style="color:var(--t3); font-size:12px;">No sections selected yet. Add sections to export template.</span>';
            return;
        }

        bulkSelectedSections.forEach(item => {
            const chip = document.createElement('div');
            chip.className = 'section-chip';
            chip.innerHTML = `
                <span>${item.label}</span>
                <span class="section-chip-close" onclick="removeBulkSectionChip(${item.section_id})">&times;</span>
            `;
            container.appendChild(chip);
        });
    }

    function downloadBulkTemplate() {
        if(bulkSelectedSections.length === 0) {
            alert('Please select at least one class and section to generate the template.');
            return;
        }

        const ids = bulkSelectedSections.map(item => item.section_id).join(',');
        window.location.href = `{{ route('school.assignments.teachers.export-template') }}?section_ids=${ids}`;
    }

    // CSV File Select & Processing
    function handleFileSelect(e) {
        const files = e.target.files;
        if(files.length > 0) {
            document.getElementById('uploadFileName').innerText = files[0].name;
        }
    }

    function submitBulkMappingFile() {
        const fileInput = document.getElementById('csvFileInput');
        if (fileInput.files.length === 0) {
            alert('Please choose a CSV file first.');
            return;
        }

        const formData = new FormData();
        formData.append('csv_file', fileInput.files[0]);

        const errBox = document.getElementById('importErrorBox');
        errBox.style.display = 'none';
        errBox.innerHTML = '';

        fetch("{{ route('school.assignments.teachers.import') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeBulkUploadDrawer();
                loadGridData();
            } else {
                errBox.style.display = 'block';
                let errHtml = `<strong>Errors occurred:</strong><ul style="margin-top: 6px; padding-left: 16px;">`;
                if(data.errors) {
                    data.errors.forEach(err => {
                        errHtml += `<li>${err}</li>`;
                    });
                } else {
                    errHtml += `<li>${data.message || 'Verification failed.'}</li>`;
                }
                errHtml += `</ul>`;
                errBox.innerHTML = errHtml;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error occurred during CSV upload.');
        });
    }

    // Logs TIMELINE Drawer
    function openLogsDrawer() {
        const container = document.getElementById('logsTimeline');
        container.innerHTML = '<div style="padding:16px; text-align:center;"><i class="fas fa-spinner fa-spin"></i> Loading logs...</div>';
        
        document.getElementById('drawerBackdrop').classList.add('active');
        document.getElementById('logsDrawer').classList.add('active');

        fetch("{{ route('school.assignments.teachers.logs') }}")
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
                            ${log.new_value ? `<div style="font-size:11px; color:#2e7d32; background:#f4faf4; padding:4px 8px; border-radius:4px; margin-top:4px;"><strong>Details:</strong> ${log.new_value}</div>` : ''}
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div style="padding:16px; text-align:center; color:var(--t3);">No teacher assignment activity logs available.</div>';
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
        closeBulkUploadDrawer();
        closeLogsDrawer();
    }
</script>
@endsection
