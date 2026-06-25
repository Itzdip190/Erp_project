@extends('layouts.app')
@section('title', 'Class Time Table')
@section('page-title', 'Class Time Table')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════════════════════════
   SHARED MODAL & FORM CSS
   ══════════════════════════════════════════════════════════════ */
.inst-modal {
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(15,23,42,0.45);
    backdrop-filter: blur(4px);
    display: none; align-items: center; justify-content: center;
    z-index: 1050; padding: 20px;
}
.inst-modal.active { display: flex; animation: cttFadeIn 0.25s ease; }
.inst-modal-content {
    background: #fff; border-radius: 16px; width: 100%; max-width: 560px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;
    overflow: hidden; animation: cttSlideUp 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.inst-modal-hdr {
    background: linear-gradient(135deg,#0d2d6e,#1e3a8a);
    padding: 18px 24px; color: #fff;
    display: flex; justify-content: space-between; align-items: center;
}
.inst-modal-hdr h3 { margin:0; font-size:16px; font-weight:800; letter-spacing:-0.5px; }
.inst-modal-close { background:none; border:none; color:rgba(255,255,255,0.75); font-size:22px; cursor:pointer; line-height:1; padding:0; }
.inst-modal-close:hover { color:#fff; }
.inst-modal-body { padding:24px; max-height:75vh; overflow-y:auto; }
.inst-form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
.inst-form-label { font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.4px; }
.inst-form-control {
    padding:10px 14px; border:1.5px solid #cbd5e1; border-radius:8px;
    font-size:13.5px; color:#1e293b; outline:none; transition:all 0.2s;
    width:100%; background:#fff; font-family:inherit;
}
.inst-form-control:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.inst-form-footer {
    display:flex; justify-content:flex-end; gap:10px;
    margin-top:24px; border-top:1px solid #f1f5f9; padding-top:16px;
}
@keyframes cttFadeIn { from{opacity:0} to{opacity:1} }
@keyframes cttSlideUp { from{transform:translateY(24px);opacity:0} to{transform:translateY(0);opacity:1} }

/* ══════════════════════════════════════════════════════════════
   CLASS TIMETABLE — Main Layout
   ══════════════════════════════════════════════════════════════ */
.ctt-page {
    font-family: 'Outfit', sans-serif;
    background: #f0f2f5;
    padding-bottom: 40px;
}

/* ── Filter Bar ───────────────────────────────────────────── */
.ctt-filter-bar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 24px;
    display: flex;
    align-items: flex-end;
    gap: 20px;
    flex-wrap: wrap;
}
.ctt-filter-group { display: flex; flex-direction: column; gap: 5px; }
.ctt-filter-lbl {
    font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    display: flex; align-items: center; gap: 5px;
}
.ctt-filter-lbl .req { color:#ef4444; }
.ctt-select {
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    background: #fff;
    min-width: 155px;
    outline: none;
    font-family: inherit;
    cursor: pointer;
    color: #334155;
    transition: border-color 0.2s;
}
.ctt-select:focus { border-color: #2563eb; }
.ctt-filter-actions { margin-left: auto; display: flex; gap: 10px; align-items: center; }
.ctt-btn-download {
    display: flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border: 1.5px solid #475569; border-radius: 8px;
    font-size: 12px; font-weight: 700;
    cursor: pointer; background: #fff; color: #334155;
    text-decoration: none; transition: all 0.2s;
}
.ctt-btn-download:hover { background: #f1f5f9; }
.ctt-btn-icon {
    width: 38px; height: 38px;
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    cursor: pointer; background: #fff; color: #64748b;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; transition: all 0.2s;
}
.ctt-btn-icon:hover { background: #f1f5f9; border-color: #cbd5e1; color: #2563eb; }

/* ── Palette Section ──────────────────────────────────────── */
.ctt-palette-wrap {
    margin: 18px 24px 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.ctt-palette-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 10px;
}
.ctt-palette-info {
    font-size: 13px; color: #475569; font-weight: 600;
}
.ctt-assign-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    border: 1.5px solid #cbd5e1; border-radius: 8px;
    background: #fff; font-size: 12px; font-weight: 700;
    color: #475569; cursor: pointer; transition: all 0.2s;
    font-family: inherit;
}
.ctt-assign-btn:hover { background: #f8fafc; border-color: #94a3b8; }
.ctt-chips-row { display: flex; flex-wrap: wrap; gap: 10px; }
.ctt-subject-chip {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 7px 14px 7px 12px;
    border-radius: 24px; cursor: grab;
    font-size: 13px; font-weight: 700;
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s; user-select: none;
    background: #f1f5f9; color: #334155;
    position: relative;
}
.ctt-subject-chip:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.ctt-subject-chip.chip-dragging { opacity: 0.5; cursor: grabbing; transform: scale(0.95); }
.ctt-chip-unassigned-mark { color: #ef4444; font-size: 14px; font-weight: 900; line-height: 1; }
.ctt-chip-assign-btn {
    background: rgba(0,0,0,0.06); border: none; cursor: pointer;
    padding: 3px 6px; border-radius: 50%;
    font-size: 12px; color: inherit; opacity: 0.8;
    transition: all 0.2s; display: flex; align-items: center;
}
.ctt-chip-assign-btn:hover { opacity: 1; background: rgba(0,0,0,0.12); }
.ctt-unassigned-warning {
    margin-top: 10px; font-size: 12px; color: #ef4444; font-weight: 700;
}

/* ── Grid Area ────────────────────────────────────────────── */
.ctt-grid-wrap { margin: 16px 24px 0; }
.ctt-empty-state {
    background: #fff; border: 2px dashed #cbd5e1;
    border-radius: 16px; padding: 60px 40px; text-align: center;
}
.ctt-empty-icon { font-size: 48px; color: #94a3b8; margin-bottom: 16px; }
.ctt-empty-title { font-size: 19px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
.ctt-empty-sub { font-size: 13.5px; color: #64748b; line-height: 1.6; }
.ctt-empty-sub a { color: #2563eb; font-weight: 700; }
.ctt-loading {
    text-align: center; padding: 60px 24px; background: #fff;
    border-radius: 12px; border: 1px solid #e2e8f0;
}

@keyframes cttToastIn { from { transform: translateX(100px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
@endsection

@section('content')
<div class="ctt-page">

    {{-- ── FILTER BAR ──────────────────────────────────────────── --}}
    <div class="ctt-filter-bar">
        <form id="filterForm" method="GET" action="{{ route('school.timetable.class') }}" style="display:contents;">
            <div class="ctt-filter-group">
                <span class="ctt-filter-lbl"><i class="fas fa-calendar-alt"></i> Academic Year <span class="req">*</span></span>
                <select name="academic_session_id" id="academic_session_id" class="ctt-select" onchange="handleSessionChange()">
                    @foreach($academicSessions as $session)
                        <option value="{{ $session->id }}" {{ $sessionId == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="ctt-filter-group">
                <span class="ctt-filter-lbl"><i class="fas fa-school"></i> Select Class</span>
                <select name="class_id" id="class_id" class="ctt-select" onchange="handleClassChange()">
                    <option value="">— Select Class —</option>
                    @foreach($classList as $class)
                        <option value="{{ $class->id }}" {{ $classFilterId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="ctt-filter-group">
                <span class="ctt-filter-lbl"><i class="fas fa-users"></i> Select Section</span>
                <select name="section_id" id="section_id" class="ctt-select" onchange="handleSectionChange()">
                    <option value="">— Select Section —</option>
                    @foreach($sectionList as $sec)
                        <option value="{{ $sec->id }}" {{ $sectionFilterId == $sec->id ? 'selected' : '' }}>
                            Section {{ $sec->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="ctt-filter-actions">
            <span id="download-btn-container">
                @if($classFilterId && $sectionFilterId && $group)
                    <a href="{{ route('school.timetable.class.download', ['class_id' => $classFilterId, 'section_id' => $sectionFilterId, 'academic_session_id' => $sessionId]) }}" class="ctt-btn-download">
                        <i class="fas fa-download"></i> DOWNLOAD
                    </a>
                @endif
            </span>
            <button class="ctt-btn-icon" onclick="refreshTimetableGrid()" title="Refresh grid">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    {{-- ── SUBJECT PALETTE ─────────────────────────────────────── --}}
    <div id="timetable-palette-container">
        @if($classFilterId && $sectionFilterId)
            @include('school.timetable.class._palette-partial', ['subjects' => $subjects])
        @endif
    </div>

    {{-- ── GRID AREA ───────────────────────────────────────────── --}}
    <div class="ctt-grid-wrap">
        <div id="timetable-grid-container">
            @if(!$classFilterId || !$sectionFilterId)
                <div class="ctt-empty-state">
                    <div class="ctt-empty-icon"><i class="fas fa-calendar-week"></i></div>
                    <h3 class="ctt-empty-title">Select Class & Section</h3>
                    <p class="ctt-empty-sub">Choose an academic year, class, and section from the filters above to load the weekly timetable grid.</p>
                </div>
            @else
                <div class="ctt-loading">
                    <i class="fas fa-spinner fa-spin" style="font-size:30px; color:#2563eb; display:block; margin-bottom:12px;"></i>
                    <div style="font-weight:700; color:#64748b; font-size:14px;">Loading timetable...</div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODALS
     ══════════════════════════════════════════════════════════════ --}}

{{-- Assign Teacher Modal --}}
<div class="inst-modal" id="modal-assign-teacher">
    <div class="inst-modal-content">
        <div class="inst-modal-hdr">
            <h3><i class="fas fa-user-plus" style="margin-right:8px;opacity:0.8;"></i> Assign Subject Teacher</h3>
            <button class="inst-modal-close" onclick="closeInstModal('modal-assign-teacher')">&times;</button>
        </div>
        <div class="inst-modal-body">
            <p style="font-size:13.5px; color:#64748b; margin-bottom:18px; line-height:1.6;">
                Subject "<strong id="assign-subject-name" style="color:#1e293b;"></strong>" has no teacher assigned for this class section. Assign one to schedule this slot.
            </p>
            <div class="inst-form-group">
                <label class="inst-form-label">Choose Teacher *</label>
                <select id="assign_teacher_id" class="inst-form-control" required>
                    <option value="">— Select Teacher —</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->full_name }} {{ $t->code ? '('.$t->code.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" id="assign_subject_id">
            <input type="hidden" id="assign_period_id">
            <input type="hidden" id="assign_day">
            <div class="inst-form-footer">
                <button type="button" onclick="closeInstModal('modal-assign-teacher')" style="padding:10px 20px; border-radius:8px; border:1.5px solid #e2e8f0; background:#fff; font-weight:700; cursor:pointer; font-family:inherit;">Cancel</button>
                <button type="button" onclick="submitTeacherAssignment()" id="assign-teacher-btn" style="padding:10px 24px; border-radius:8px; background:#2563eb; color:#fff; border:none; font-weight:700; cursor:pointer; font-family:inherit; font-size:13.5px;">Assign & Schedule</button>
            </div>
        </div>
    </div>
</div>

{{-- Replicate Cell Modal --}}
<div class="inst-modal" id="modal-copy-cell">
    <div class="inst-modal-content">
        <div class="inst-modal-hdr">
            <h3><i class="far fa-copy" style="margin-right:8px;opacity:0.8;"></i> Replicate Scheduled Slot</h3>
            <button class="inst-modal-close" onclick="closeInstModal('modal-copy-cell')">&times;</button>
        </div>
        <div class="inst-modal-body">
            <p style="font-size:13.5px; color:#64748b; margin-bottom:18px; line-height:1.6;">
                Replicate "<strong id="copy-subject-name" style="color:#1e293b;"></strong>" to the same period on other weekdays.
            </p>
            <input type="hidden" id="copy_cell_id">
            <div class="inst-form-group">
                <label class="inst-form-label">Select Target Days *</label>
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-top:6px;">
                    @foreach($days as $day)
                        <label id="lbl-copy-day-{{ $day }}" style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; cursor:pointer; padding:6px 10px; border-radius:8px; border:1.5px solid #e2e8f0; transition:all 0.2s;">
                            <input type="checkbox" name="copy_days[]" value="{{ $day }}" class="copy-day-chk" style="accent-color:#2563eb; width:14px; height:14px;">
                            {{ $day }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="inst-form-footer">
                <button type="button" onclick="closeInstModal('modal-copy-cell')" style="padding:10px 20px; border-radius:8px; border:1.5px solid #e2e8f0; background:#fff; font-weight:700; cursor:pointer; font-family:inherit;">Cancel</button>
                <button type="button" onclick="submitCellReplication()" style="padding:10px 24px; border-radius:8px; background:#2563eb; color:#fff; border:none; font-weight:700; cursor:pointer; font-family:inherit; font-size:13.5px;">Replicate Slot</button>
            </div>
        </div>
    </div>
</div>

{{-- Add Subject Picker Modal --}}
<div class="inst-modal" id="modal-add-subject">
    <div class="inst-modal-content">
        <div class="inst-modal-hdr">
            <h3><i class="fas fa-plus-circle" style="margin-right:8px;opacity:0.8;"></i> Add Subject to Slot</h3>
            <button class="inst-modal-close" onclick="closeInstModal('modal-add-subject')">&times;</button>
        </div>
        <div class="inst-modal-body">
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Choose a subject to schedule for this period:</p>
            <input type="hidden" id="add_period_id">
            <input type="hidden" id="add_day_value">
            <div id="add-subject-chips-list" style="display:flex; flex-wrap:wrap; gap:10px; min-height:40px; margin-bottom:20px;"></div>
            <div class="inst-form-footer">
                <button type="button" onclick="closeInstModal('modal-add-subject')" style="padding:10px 20px; border-radius:8px; border:1.5px solid #e2e8f0; background:#fff; font-weight:700; cursor:pointer; font-family:inherit;">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const classTimetableConfig = {
        classId:        "{{ $classFilterId }}",
        sectionId:      "{{ $sectionFilterId }}",
        sessionId:      "{{ $sessionId }}",
        csrfToken:      "{{ csrf_token() }}",
        groupId:        "{{ $group ? $group->id : '' }}",
        gridUrl:        "/school/timetable/class/grid",
        saveCellUrl:    "/school/timetable/class/cell",
        checkTeacherUrl:"/school/timetable/check-teacher",
        assignTeacherUrl:"/school/timetable/assign-teacher"
    };

    function loadSectionsAndReload() {
        const classId = document.getElementById('class_id').value;
        const sessionId = document.getElementById('academic_session_id').value;
        window.location.href = `/school/timetable/class?class_id=${classId}&academic_session_id=${sessionId}`;
    }

    function openQuickAssign(subjectId, subjectName) {
        document.getElementById('assign_subject_id').value = subjectId;
        document.getElementById('assign_period_id').value = '';
        document.getElementById('assign_day').value = '';
        document.getElementById('assign-subject-name').textContent = subjectName;
        document.getElementById('assign_teacher_id').value = '';
        openInstModal('modal-assign-teacher');
    }

    function openBulkAssignModal() {
        showToast('Use the 👤 icon on each subject chip to assign teachers individually.', 'info');
    }

    function openAddSubjectModal(periodId, day) {
        document.getElementById('add_period_id').value = periodId;
        document.getElementById('add_day_value').value = day;

        const list = document.getElementById('add-subject-chips-list');
        list.innerHTML = '';

        const chips = document.querySelectorAll('.ctt-subject-chip[data-subject-id]');
        if (chips.length === 0) {
            list.innerHTML = '<p style="color:#94a3b8; font-style:italic; font-size:13px;">No subjects available.</p>';
        } else {
            chips.forEach(chip => {
                const subjectId = chip.dataset.subjectId;
                const subjectName = chip.dataset.subjectName;
                const color = getSubjectColor(subjectId);
                const btn = document.createElement('button');
                btn.style.cssText = `padding:8px 18px; border-radius:20px; border:1.5px solid ${color.border}; background:${color.bg}; color:${color.text}; font-weight:700; font-size:13px; cursor:pointer; transition:all 0.2s; font-family:inherit;`;
                btn.textContent = subjectName;
                btn.onmouseenter = () => btn.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
                btn.onmouseleave = () => btn.style.boxShadow = 'none';
                btn.onclick = () => {
                    closeInstModal('modal-add-subject');
                    handleCellDrop(subjectId, periodId, day);
                };
                list.appendChild(btn);
            });
        }

        openInstModal('modal-add-subject');
    }
</script>
@php
    $assetPrefix = str_contains(request()->getHost(), 'hostinger') ? 'public/' : '';
@endphp
<script src="{{ asset($assetPrefix . 'js/timetable.js') }}?v={{ time() }}"></script>
@endsection
