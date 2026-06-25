/* ══════════════════════════════════════════════════════════════════════
   TIMETABLE.JS  — Class Timetable Frontend Controller
   Uses native HTML5 Drag & Drop API (no SortableJS dependency)
   ══════════════════════════════════════════════════════════════════════ */

'use strict';

// ── Subject Colour Palette ─────────────────────────────────────────────
const SUBJECT_COLORS = [
    { bg: '#fce4ec', border: '#f48fb1', text: '#880e4f', cardBg: '#fce4ec' }, // pink
    { bg: '#ede7f6', border: '#ce93d8', text: '#4a148c', cardBg: '#ede7f6' }, // purple
    { bg: '#e8f5e9', border: '#a5d6a7', text: '#1b5e20', cardBg: '#e8f5e9' }, // green
    { bg: '#e3f2fd', border: '#90caf9', text: '#0d47a1', cardBg: '#e3f2fd' }, // blue
    { bg: '#fff8e1', border: '#ffe082', text: '#e65100', cardBg: '#fff8e1' }, // amber
    { bg: '#e0f2f1', border: '#80cbc4', text: '#004d40', cardBg: '#e0f2f1' }, // teal
    { bg: '#fce8f3', border: '#f48bcd', text: '#880e4f', cardBg: '#fce8f3' }, // rose
    { bg: '#e8eaf6', border: '#9fa8da', text: '#1a237e', cardBg: '#e8eaf6' }, // indigo
];

/** Returns colour object for a given subject ID (consistent via modulo) */
function getSubjectColor(subjectId) {
    return SUBJECT_COLORS[Math.abs(parseInt(subjectId) || 0) % SUBJECT_COLORS.length];
}

// ── Day Mode Tracker ───────────────────────────────────────────────────
const dayModes = {
    Monday: 'online', Tuesday: 'online', Wednesday: 'online',
    Thursday: 'online', Friday: 'online', Saturday: 'online', Sunday: 'online'
};

function setDayMode(day, mode) {
    dayModes[day] = mode;
}
function getModeForDay(day) {
    return dayModes[day] || 'online';
}

// ── Modal Helpers ──────────────────────────────────────────────────────
function openInstModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
}
function closeInstModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
}

// ── Toast Notifications ────────────────────────────────────────────────
function showToast(message, type) {
    type = type || 'success';
    const existing = document.getElementById('ctt-global-toast');
    if (existing) existing.remove();

    const colors  = { success: '#10b981', error: '#ef4444', info: '#3b82f6', warning: '#f59e0b' };
    const icons   = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle', warning: 'exclamation-triangle' };

    const toast = document.createElement('div');
    toast.id = 'ctt-global-toast';
    toast.style.cssText = [
        'position:fixed', 'bottom:28px', 'right:28px', 'z-index:99999',
        `background:${colors[type] || colors.success}`, 'color:#fff',
        'padding:13px 20px', 'border-radius:10px',
        'font-weight:700', 'font-size:13.5px',
        'box-shadow:0 8px 28px rgba(0,0,0,0.2)',
        'display:flex', 'align-items:center', 'gap:10px',
        "font-family:'Outfit',sans-serif",
        'animation:cttToastIn 0.3s ease',
        'max-width:340px', 'line-height:1.4'
    ].join(';');
    toast.innerHTML = `<i class="fas fa-${icons[type] || icons.success}" style="flex-shrink:0;"></i><span>${message}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.5s, transform 0.5s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(60px)';
        setTimeout(() => toast.remove(), 500);
    }, 3200);
}

// ── Apply Subject Colours ──────────────────────────────────────────────
function applySubjectColors() {
    // Palette chips
    document.querySelectorAll('.ctt-subject-chip[data-subject-id]').forEach(chip => {
        const c = getSubjectColor(chip.dataset.subjectId);
        chip.style.background    = c.bg;
        chip.style.borderColor   = c.border;
        chip.style.color         = c.text;
    });

    // Grid cards
    document.querySelectorAll('.ttg-subject-card[data-subject-id]').forEach(card => {
        const c = getSubjectColor(card.dataset.subjectId);
        card.style.background     = c.cardBg;
        card.style.borderLeftColor = c.border;
        const subEl = card.querySelector('.ttg-card-subject');
        if (subEl) subEl.style.color = c.text;
    });
}

// ── Grid Loading ───────────────────────────────────────────────────────
function refreshTimetableGrid() {
    const container = document.getElementById('timetable-grid-container');
    if (!container) return;
    if (!classTimetableConfig.classId || !classTimetableConfig.sectionId) {
        showDefaultEmptyState(container);
        return;
    }

    const isAlreadyLoaded = container.querySelector('.ttg-table') !== null;
    if (isAlreadyLoaded) {
        container.style.opacity = '0.65';
        container.style.pointerEvents = 'none';
    } else {
        container.innerHTML = `
            <div class="ctt-loading" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:60px 24px;text-align:center;">
                <i class="fas fa-spinner fa-spin" style="font-size:30px;color:#2563eb;display:block;margin-bottom:14px;"></i>
                <div style="font-weight:700;color:#64748b;font-size:14px;font-family:'Outfit',sans-serif;">Loading timetable grid…</div>
            </div>`;
    }

    const url = classTimetableConfig.gridUrl
        + '?class_id='           + encodeURIComponent(classTimetableConfig.classId)
        + '&section_id='         + encodeURIComponent(classTimetableConfig.sectionId)
        + '&academic_session_id='+ encodeURIComponent(classTimetableConfig.sessionId);

    fetch(url)
        .then(res => {
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
            if (res.status === 404) {
                return res.json().then(data => {
                    if (data.palette_html) {
                        const paletteContainer = document.getElementById('timetable-palette-container');
                        if (paletteContainer) paletteContainer.innerHTML = data.palette_html;
                    }
                    showNoGroupState(container);
                    throw new Error("No template group");
                });
            }
            if (!res.ok) throw new Error(res.status);
            return res.json();
        })
        .then(data => {
            if (data.success && data.html) {
                container.innerHTML = data.html;
                if (data.palette_html) {
                    const paletteContainer = document.getElementById('timetable-palette-container');
                    if (paletteContainer) {
                        paletteContainer.innerHTML = data.palette_html;
                    }
                }
                initDragAndDrop();
                applySubjectColors();
                
                if (data.group_id) {
                    classTimetableConfig.groupId = data.group_id;
                }
                updateDownloadButton();
            } else {
                showNoGroupState(container);
                const paletteContainer = document.getElementById('timetable-palette-container');
                if (paletteContainer) paletteContainer.innerHTML = '';
            }
        })
        .catch(err => {
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
            if (err.message !== "No template group") {
                showNoGroupState(container);
                const paletteContainer = document.getElementById('timetable-palette-container');
                if (paletteContainer) paletteContainer.innerHTML = '';
            }
        });
}

function showNoGroupState(container) {
    container.innerHTML = `
        <div class="ctt-empty-state" style="background:#fff;border:2px dashed #e2e8f0;border-radius:16px;padding:60px 40px;text-align:center;">
            <div style="font-size:48px;color:#f59e0b;margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i></div>
            <h3 style="font-size:19px;font-weight:800;color:#1e293b;margin-bottom:8px;">No Timetable Template Assigned</h3>
            <p style="font-size:13.5px;color:#64748b;line-height:1.6;">
                No active Group Template is allocated to this Class & Section.<br>
                Go to <a href="/school/timetable/groups" style="color:#2563eb;font-weight:700;">Group-Wise Timetable</a> and create or assign a template first.
            </p>
        </div>`;
}

// ── Drag & Drop Initialisation ─────────────────────────────────────────
function initDragAndDrop() {
    // Sync day-mode selectors
    document.querySelectorAll('.ttg-mode-select').forEach(sel => {
        dayModes[sel.dataset.day] = sel.value;
    });

    // ── PALETTE CHIP DRAG SOURCE ──────────────────────────────────────
    document.querySelectorAll('.ctt-subject-chip[data-subject-id]').forEach(chip => {
        chip.setAttribute('draggable', 'true');

        chip.addEventListener('dragstart', e => {
            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/x-subject-id',   chip.dataset.subjectId);
            e.dataTransfer.setData('text/x-subject-name', chip.dataset.subjectName);
            e.dataTransfer.setData('text/x-source-type',  'palette');
            chip.classList.add('chip-dragging');
        });
        chip.addEventListener('dragend', () => chip.classList.remove('chip-dragging'));
    });

    // ── FILLED CARD DRAG SOURCE (cell-to-cell copy) ───────────────────
    document.querySelectorAll('.ttg-subject-card[data-cell-id]').forEach(card => {
        card.setAttribute('draggable', 'true');
        card.addEventListener('dragstart', e => {
            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/x-subject-id',  card.dataset.subjectId);
            e.dataTransfer.setData('text/x-source-type', 'cell');
            e.dataTransfer.setData('text/x-source-cell', card.dataset.cellId);
            card.classList.add('card-dragging');
        });
        card.addEventListener('dragend', () => card.classList.remove('card-dragging'));
    });

    // ── DROP TARGETS (active cells) ───────────────────────────────────
    document.querySelectorAll('.ttg-cell.ttg-cell-active').forEach(cell => {
        cell.addEventListener('dragover', e => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
            cell.classList.add('drag-over');
        });
        cell.addEventListener('dragleave', e => {
            if (!cell.contains(e.relatedTarget)) cell.classList.remove('drag-over');
        });
        cell.addEventListener('drop', e => {
            e.preventDefault();
            cell.classList.remove('drag-over');

            const sourceType  = e.dataTransfer.getData('text/x-source-type');
            const subjectId   = e.dataTransfer.getData('text/x-subject-id');
            const periodId    = cell.dataset.periodId;
            const day         = cell.dataset.day;

            if (!periodId || !day) return;

            if (sourceType === 'cell') {
                const sourceCellId = e.dataTransfer.getData('text/x-source-cell');
                copyCellToTarget(sourceCellId, periodId, day);
            } else {
                if (subjectId) handleCellDrop(subjectId, periodId, day, cell);
            }
        });
    });

    // Close dropdowns on outside click
    document.addEventListener('click', e => {
        if (!e.target.closest('.ttg-menu-btn') && !e.target.closest('.ttg-dropdown')) {
            document.querySelectorAll('.ttg-dropdown.open').forEach(d => d.classList.remove('open'));
        }
    }, true);
}

// ── Cell Operations ────────────────────────────────────────────────────

function handleCellDrop(subjectId, periodId, dayOfWeek, targetCell) {
    // Show spinner in the target cell
    if (targetCell) {
        targetCell.innerHTML = `
            <div style="display:flex;justify-content:center;align-items:center;height:100%;min-height:100px;">
                <i class="fas fa-spinner fa-spin" style="color:#2563eb;font-size:20px;"></i>
            </div>`;
    }

    const checkUrl = classTimetableConfig.checkTeacherUrl
        + '?class_id='   + encodeURIComponent(classTimetableConfig.classId)
        + '&section_id=' + encodeURIComponent(classTimetableConfig.sectionId)
        + '&subject_id=' + encodeURIComponent(subjectId);

    fetch(checkUrl)
        .then(res => res.json())
        .then(data => {
            if (data.assigned) {
                saveCell(subjectId, periodId, dayOfWeek);
            } else {
                // Need to assign teacher first
                const chipEl = document.querySelector(`.ctt-subject-chip[data-subject-id="${subjectId}"]`);
                const subjectName = chipEl ? chipEl.dataset.subjectName : 'Subject';
                openTeacherAssignModal(subjectId, subjectName, periodId, dayOfWeek);
                refreshTimetableGrid(); // restore cell
            }
        })
        .catch(() => refreshTimetableGrid());
}

function saveCell(subjectId, periodId, dayOfWeek) {
    fetch(classTimetableConfig.saveCellUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': classTimetableConfig.csrfToken
        },
        body: JSON.stringify({
            timetable_group_id:         classTimetableConfig.groupId,
            class_id:                   classTimetableConfig.classId,
            section_id:                 classTimetableConfig.sectionId,
            timetable_group_period_id:  periodId,
            day_of_week:               dayOfWeek,
            subject_id:                subjectId,
            mode:                      getModeForDay(dayOfWeek)
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            refreshTimetableGrid();
        } else {
            showToast(data.message || 'Failed to save slot.', 'error');
            refreshTimetableGrid();
        }
    })
    .catch(() => { showToast('Something went wrong.', 'error'); refreshTimetableGrid(); });
}

function copyCellToTarget(sourceCellId, targetPeriodId, targetDay) {
    const url = classTimetableConfig.saveCellUrl.replace(/\/cell$/, '/cell/') + sourceCellId + '/copy';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': classTimetableConfig.csrfToken
        },
        body: JSON.stringify({
            targets: [{ timetable_group_period_id: targetPeriodId, day_of_week: targetDay }]
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Slot copied!', 'success');
            refreshTimetableGrid();
        } else {
            showToast(data.message || 'Copy failed.', 'error');
        }
    })
    .catch(() => showToast('Something went wrong.', 'error'));
}

function toggleCellMode(cellId, currentMode, subjectId) {
    document.querySelectorAll('.ttg-dropdown.open').forEach(d => d.classList.remove('open'));
    const newMode = currentMode === 'online' ? 'offline' : 'online';

    const card = document.querySelector(`.ttg-subject-card[data-cell-id="${cellId}"]`);
    const td   = card ? card.closest('td') : null;
    if (!td) return;

    fetch(classTimetableConfig.saveCellUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': classTimetableConfig.csrfToken
        },
        body: JSON.stringify({
            timetable_group_id:        classTimetableConfig.groupId,
            class_id:                  classTimetableConfig.classId,
            section_id:                classTimetableConfig.sectionId,
            timetable_group_period_id: td.dataset.periodId,
            day_of_week:              td.dataset.day,
            subject_id:               subjectId,
            mode:                     newMode
        })
    })
    .then(res => res.json())
    .then(data => { if (data.success) refreshTimetableGrid(); })
    .catch(() => {});
}

function deleteScheduledPeriod(cellId) {
    document.querySelectorAll('.ttg-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!confirm('Remove this scheduled period slot?')) return;

    const url = classTimetableConfig.saveCellUrl.replace(/\/cell$/, '/cell/') + cellId;

    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': classTimetableConfig.csrfToken }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { showToast('Slot removed.', 'success'); refreshTimetableGrid(); }
        else showToast('Failed to remove slot.', 'error');
    })
    .catch(() => showToast('Something went wrong.', 'error'));
}

// ── Context Menu Toggle ────────────────────────────────────────────────
function toggleCellMenu(event, cellId) {
    event.stopPropagation();
    const menu = document.getElementById('dropdown-' + cellId);
    if (!menu) return;
    // Close others
    document.querySelectorAll('.ttg-dropdown.open').forEach(d => {
        if (d !== menu) d.classList.remove('open');
    });
    menu.classList.toggle('open');
}

// ── Replicate Cell Modal ───────────────────────────────────────────────
function openCopyToDaysModal(cellId, subjectName, currentDay) {
    document.querySelectorAll('.ttg-dropdown.open').forEach(d => d.classList.remove('open'));
    document.getElementById('copy_cell_id').value = cellId;
    document.getElementById('copy-subject-name').textContent = subjectName;

    document.querySelectorAll('.copy-day-chk').forEach(chk => {
        chk.checked = false;
        const lbl = document.getElementById('lbl-copy-day-' + chk.value);
        if (lbl) lbl.style.display = chk.value === currentDay ? 'none' : 'flex';
    });

    openInstModal('modal-copy-cell');
}

function submitCellReplication() {
    const cellId = document.getElementById('copy_cell_id').value;
    const checkedDays = [...document.querySelectorAll('.copy-day-chk:checked')].map(c => c.value);

    if (checkedDays.length === 0) { showToast('Please select at least one target day.', 'error'); return; }

    const card = document.querySelector(`.ttg-subject-card[data-cell-id="${cellId}"]`);
    const td   = card ? card.closest('td') : null;
    const periodId = td ? td.dataset.periodId : null;

    if (!periodId) { showToast('Could not find period. Refresh and try again.', 'error'); return; }

    const targets = checkedDays.map(day => ({
        timetable_group_period_id: periodId,
        day_of_week: day
    }));

    const url = classTimetableConfig.saveCellUrl.replace(/\/cell$/, '/cell/') + cellId + '/copy';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': classTimetableConfig.csrfToken
        },
        body: JSON.stringify({ targets })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeInstModal('modal-copy-cell');
            showToast('Slot replicated to ' + checkedDays.length + ' day(s)!', 'success');
            refreshTimetableGrid();
        } else {
            showToast(data.message || 'Failed to replicate.', 'error');
        }
    })
    .catch(() => showToast('Something went wrong.', 'error'));
}

// ── Teacher Assignment ─────────────────────────────────────────────────
function openTeacherAssignModal(subjectId, subjectName, periodId, dayOfWeek) {
    document.getElementById('assign_subject_id').value    = subjectId;
    document.getElementById('assign_period_id').value     = periodId  || '';
    document.getElementById('assign_day').value           = dayOfWeek || '';
    document.getElementById('assign-subject-name').textContent = subjectName;
    document.getElementById('assign_teacher_id').value    = '';
    openInstModal('modal-assign-teacher');
}

function submitTeacherAssignment() {
    const teacherId  = document.getElementById('assign_teacher_id').value;
    const subjectId  = document.getElementById('assign_subject_id').value;
    const periodId   = document.getElementById('assign_period_id').value;
    const dayOfWeek  = document.getElementById('assign_day').value;

    if (!teacherId) { showToast('Please select a teacher.', 'error'); return; }

    const btn = document.getElementById('assign-teacher-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Assigning…'; }

    const payload = {
        class_id:                  classTimetableConfig.classId,
        section_id:                classTimetableConfig.sectionId,
        subject_id:               subjectId,
        teacher_id:               teacherId
    };

    // If we also have a period+day, schedule the cell right after assigning teacher
    if (periodId && dayOfWeek) {
        payload.timetable_group_id         = classTimetableConfig.groupId;
        payload.timetable_group_period_id  = periodId;
        payload.day_of_week               = dayOfWeek;
        payload.mode                      = getModeForDay(dayOfWeek);
    }

    fetch(classTimetableConfig.assignTeacherUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': classTimetableConfig.csrfToken
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.textContent = 'Assign & Schedule'; }
        if (data.success) {
            closeInstModal('modal-assign-teacher');
            showToast('Teacher assigned successfully!', 'success');
            refreshTimetableGrid();
        } else {
            showToast(data.message || 'Failed to assign teacher.', 'error');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Assign & Schedule'; }
        showToast('Something went wrong.', 'error');
    });
}

// ── Period Time Edit (placeholder) ────────────────────────────────────
function editPeriodTime(periodId) {
    showToast('Period editing: go to Group Timetable to modify period times.', 'info');
}

// ── Page Init ─────────────────────────────────────────────────────────
function initClassTimetable() {
    // Colour the palette chips immediately
    applySubjectColors();

    // Set up palette chip drag events (even before grid loads)
    document.querySelectorAll('.ctt-subject-chip[data-subject-id]').forEach(chip => {
        chip.setAttribute('draggable', 'true');
        chip.addEventListener('dragstart', e => {
            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/x-subject-id',   chip.dataset.subjectId);
            e.dataTransfer.setData('text/x-subject-name', chip.dataset.subjectName);
            e.dataTransfer.setData('text/x-source-type',  'palette');
            chip.classList.add('chip-dragging');
        });
        chip.addEventListener('dragend', () => chip.classList.remove('chip-dragging'));
    });

    // Load grid if class+section selected
    if (classTimetableConfig.classId && classTimetableConfig.sectionId) {
        refreshTimetableGrid();
    }

    // Close modals on backdrop click
    document.querySelectorAll('.inst-modal').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeInstModal(modal.id);
        });
    });
}

// ── Filter Dropdown AJAX Handlers ─────────────────────────────────────
function handleClassChange() {
    const classId = document.getElementById('class_id').value;
    const sessionId = document.getElementById('academic_session_id').value;

    classTimetableConfig.classId = classId;
    classTimetableConfig.sectionId = ''; // Reset section

    // Clear palette & update download button
    const paletteContainer = document.getElementById('timetable-palette-container');
    if (paletteContainer) paletteContainer.innerHTML = '';
    updateDownloadButton();

    // Show empty state for grid
    const container = document.getElementById('timetable-grid-container');
    if (container) showDefaultEmptyState(container);

    const sectionSelect = document.getElementById('section_id');
    if (!sectionSelect) return;

    if (!classId) {
        sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
        updateUrlQuery();
        return;
    }

    sectionSelect.innerHTML = '<option value="">Loading sections...</option>';

    // Fetch sections of the class
    const url = `/school/timetable/class?class_id=${encodeURIComponent(classId)}&get_sections=1`;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.sections) {
                let html = '<option value="">— Select Section —</option>';
                data.sections.forEach(sec => {
                    html += `<option value="${sec.id}">Section ${sec.name}</option>`;
                });
                sectionSelect.innerHTML = html;
            } else {
                sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
            }
            updateUrlQuery();
        })
        .catch(() => {
            sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
            updateUrlQuery();
        });
}

function handleSectionChange() {
    const sectionId = document.getElementById('section_id').value;
    classTimetableConfig.sectionId = sectionId;
    updateUrlQuery();

    if (sectionId) {
        refreshTimetableGrid();
    } else {
        const container = document.getElementById('timetable-grid-container');
        if (container) showDefaultEmptyState(container);
        const paletteContainer = document.getElementById('timetable-palette-container');
        if (paletteContainer) paletteContainer.innerHTML = '';
        updateDownloadButton();
    }
}

function handleSessionChange() {
    const sessionId = document.getElementById('academic_session_id').value;
    classTimetableConfig.sessionId = sessionId;
    updateUrlQuery();
    if (classTimetableConfig.classId && classTimetableConfig.sectionId) {
        refreshTimetableGrid();
    }
}

function updateUrlQuery() {
    const classId = classTimetableConfig.classId;
    const sectionId = classTimetableConfig.sectionId;
    const sessionId = classTimetableConfig.sessionId;
    const newUrl = `${window.location.pathname}?class_id=${encodeURIComponent(classId)}&section_id=${encodeURIComponent(sectionId)}&academic_session_id=${encodeURIComponent(sessionId)}`;
    history.pushState({ classId, sectionId, sessionId }, '', newUrl);
}

function updateDownloadButton() {
    const classId = classTimetableConfig.classId;
    const sectionId = classTimetableConfig.sectionId;
    const sessionId = classTimetableConfig.sessionId;
    const downloadContainer = document.getElementById('download-btn-container');
    if (downloadContainer) {
        if (classId && sectionId && classTimetableConfig.groupId) {
            downloadContainer.innerHTML = `
                <a href="/school/timetable/class/download?class_id=${encodeURIComponent(classId)}&section_id=${encodeURIComponent(sectionId)}&academic_session_id=${encodeURIComponent(sessionId)}" class="ctt-btn-download">
                    <i class="fas fa-download"></i> DOWNLOAD
                </a>
            `;
        } else {
            downloadContainer.innerHTML = '';
        }
    }
}

function showDefaultEmptyState(container) {
    container.innerHTML = `
        <div class="ctt-empty-state" style="background:#fff;border:2px dashed #cbd5e1;border-radius:16px;padding:60px 40px;text-align:center;">
            <div style="font-size:48px;color:#94a3b8;margin-bottom:16px;"><i class="fas fa-calendar-week"></i></div>
            <h3 style="font-size:19px;font-weight:800;color:#1e293b;margin-bottom:8px;">Select Class & Section</h3>
            <p style="font-size:13.5px;color:#64748b;line-height:1.6;">Choose an academic year, class, and section from the filters above to load the weekly timetable grid.</p>
        </div>`;
}

// ── popstate Navigation Listener ──────────────────────────────────────
window.addEventListener('popstate', e => {
    const urlParams = new URLSearchParams(window.location.search);
    const classId = urlParams.get('class_id') || '';
    const sectionId = urlParams.get('section_id') || '';
    const sessionId = urlParams.get('academic_session_id') || '';

    classTimetableConfig.classId = classId;
    classTimetableConfig.sectionId = sectionId;
    if (sessionId) classTimetableConfig.sessionId = sessionId;

    const classSelect = document.getElementById('class_id');
    const sessionSelect = document.getElementById('academic_session_id');
    if (classSelect) classSelect.value = classId;
    if (sessionSelect && sessionId) sessionSelect.value = sessionId;

    const sectionSelect = document.getElementById('section_id');
    if (sectionSelect) {
        if (!classId) {
            sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
            const container = document.getElementById('timetable-grid-container');
            if (container) showDefaultEmptyState(container);
            const paletteContainer = document.getElementById('timetable-palette-container');
            if (paletteContainer) paletteContainer.innerHTML = '';
            updateDownloadButton();
        } else {
            sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
            const url = `/school/timetable/class?class_id=${encodeURIComponent(classId)}&get_sections=1`;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.sections) {
                        let html = '<option value="">— Select Section —</option>';
                        data.sections.forEach(sec => {
                            html += `<option value="${sec.id}" ${sec.id == sectionId ? 'selected' : ''}>Section ${sec.name}</option>`;
                        });
                        sectionSelect.innerHTML = html;
                    } else {
                        sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
                    }
                    updateDownloadButton();
                    if (sectionId) {
                        refreshTimetableGrid();
                    } else {
                        const container = document.getElementById('timetable-grid-container');
                        if (container) showDefaultEmptyState(container);
                        const paletteContainer = document.getElementById('timetable-palette-container');
                        if (paletteContainer) paletteContainer.innerHTML = '';
                    }
                })
                .catch(() => {
                    sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
                });
        }
    }
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClassTimetable);
} else {
    initClassTimetable();
}
