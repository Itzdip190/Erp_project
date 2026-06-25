{{-- ══════════════════════════════════════════════════════════════
     Class Timetable — Weekly Grid Partial
     Rendered via AJAX by ClassTimetableController@getGrid
     ══════════════════════════════════════════════════════════════ --}}
@php
    use Carbon\Carbon;

    function cttFormatTime($timeStr) {
        if (!$timeStr) return '–';
        try {
            return Carbon::createFromTimeString($timeStr)->format('g:i A');
        } catch (\Exception $e) {
            return $timeStr;
        }
    }

    $className   = $section->schoolClass->name ?? '';
    $sectionName = $section->name ?? '';
    $fullName    = trim($className . ' ' . $sectionName);
@endphp

<div class="ttg-card">

    {{-- Class Name Header --}}
    <div class="ttg-class-header">{{ $fullName }}</div>

    {{-- Grid Table --}}
    <div style="overflow-x: auto;">
        <table class="ttg-table">
            <thead>
                <tr>
                    {{-- Period column header --}}
                    <th class="ttg-period-th"></th>

                    {{-- Day headers with mode selector --}}
                    @foreach($days as $day)
                        @php
                            $isActive = is_array($group->applicable_days) && in_array($day, $group->applicable_days);
                        @endphp
                        <th class="ttg-day-th {{ $isActive ? 'ttg-day-active' : 'ttg-day-inactive' }}">
                            <div class="ttg-day-name">{{ $day }}</div>
                            @if($isActive)
                                <select class="ttg-mode-select"
                                        data-day="{{ $day }}"
                                        onchange="setDayMode('{{ $day }}', this.value)">
                                    <option value="online">🖥 Online</option>
                                    <option value="offline">🏫 Offline</option>
                                </select>
                            @else
                                <div style="font-size:10px; opacity:0.6; margin-top:4px;">Off / Holiday</div>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                    <tr class="ttg-row">
                        {{-- Period Label --}}
                        <td class="ttg-period-label">
                            <div class="ttg-pname">{{ $period->period_name }}</div>
                            <div class="ttg-ptime">
                                {{ cttFormatTime($period->start_time) }}
                                @if($period->start_time && $period->end_time) – @endif
                                {{ cttFormatTime($period->end_time) }}
                            </div>
                            <button class="ttg-edit-btn" onclick="editPeriodTime({{ $period->id }})" title="Edit period">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </td>

                        {{-- Data Cells --}}
                        @foreach($days as $day)
                            @php
                                $isActive = is_array($group->applicable_days) && in_array($day, $group->applicable_days);
                                $cell = $gridData[$period->id][$day] ?? null;
                            @endphp

                            <td class="ttg-cell {{ $isActive ? 'ttg-cell-active' : 'ttg-cell-off' }}"
                                data-period-id="{{ $period->id }}"
                                data-day="{{ $day }}">

                                @if($cell)
                                    {{-- ── FILLED CELL ──────────────────────────── --}}
                                    <div class="ttg-subject-card"
                                         data-cell-id="{{ $cell->id }}"
                                         data-subject-id="{{ $cell->subject_id }}"
                                         draggable="true">

                                        {{-- "..." menu button --}}
                                        <button class="ttg-menu-btn"
                                                onclick="toggleCellMenu(event, {{ $cell->id }})"
                                                title="Actions">
                                            &bull;&bull;&bull;
                                        </button>

                                        <div class="ttg-card-subject">{{ $cell->subject->name ?? 'N/A' }}</div>
                                        <div class="ttg-card-teacher">{{ $cell->teacher->full_name ?? '' }}</div>

                                        {{-- Dropdown Actions Menu --}}
                                        <div class="ttg-dropdown" id="dropdown-{{ $cell->id }}">
                                            <button onclick="openAddSubjectModal({{ $period->id }}, '{{ $day }}')">
                                                <i class="fas fa-edit"></i>
                                                Change Class
                                            </button>
                                            <button onclick="toggleCellMode({{ $cell->id }}, '{{ $cell->mode }}', '{{ $cell->subject_id }}')">
                                                <i class="fas fa-exchange-alt"></i>
                                                Switch to {{ $cell->mode === 'online' ? 'Offline' : 'Online' }}
                                            </button>
                                            <button onclick="openCopyToDaysModal({{ $cell->id }}, '{{ addslashes($cell->subject->name ?? 'N/A') }}', '{{ $day }}')">
                                                <i class="far fa-copy"></i>
                                                Replicate to days
                                            </button>
                                            <button onclick="deleteScheduledPeriod({{ $cell->id }})" class="ttg-danger-btn">
                                                <i class="far fa-trash-alt"></i>
                                                Delete Class
                                            </button>
                                        </div>
                                    </div>

                                @elseif($isActive)
                                    {{-- ── EMPTY ACTIVE CELL ────────────────────── --}}
                                    <div class="ttg-cell-empty">
                                        <span class="ttg-empty-text">Drag and drop<br>Subjects</span>
                                        <span class="ttg-empty-or">or</span>
                                        <button class="ttg-add-btn"
                                                onclick="openAddSubjectModal({{ $period->id }}, '{{ $day }}')">
                                            <i class="fas fa-plus"></i> ADD
                                        </button>
                                    </div>

                                @else
                                    {{-- ── INACTIVE / HOLIDAY ───────────────────── --}}
                                    <div class="ttg-cell-off-content">–</div>

                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="ttg-hint">*Drag contents of any cell to copy to other cells</p>
</div>

{{-- ══════════════════════════════════════════════════════════════
     GRID STYLES (scoped to this partial)
     ══════════════════════════════════════════════════════════════ --}}
<style>
/* ── Card Container ────────────────────────────────────────── */
.ttg-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    padding-bottom: 16px;
    font-family: 'Outfit', sans-serif;
}
.ttg-class-header {
    text-align: center;
    font-size: 22px;
    font-weight: 900;
    color: #0f172a;
    padding: 18px 0 12px;
    letter-spacing: -0.5px;
    border-bottom: 1px solid #f1f5f9;
}

/* ── Table ─────────────────────────────────────────────────── */
.ttg-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
}

/* Period-column header */
.ttg-period-th {
    background: #f8fafc;
    padding: 12px 16px;
    border-bottom: 2px solid #e2e8f0;
    border-right: 2px solid #e2e8f0;
    min-width: 130px;
    width: 130px;
}

/* Day-column headers */
.ttg-day-th {
    text-align: center;
    padding: 10px 8px;
    border-bottom: 2px solid #e2e8f0;
    border-right: 1px solid rgba(255,255,255,0.1);
    min-width: 120px;
}
.ttg-day-active  { background: #1e293b; color: #fff; }
.ttg-day-inactive { background: #64748b; color: #fff; }
.ttg-day-name { font-size: 13px; font-weight: 800; margin-bottom: 6px; }
.ttg-mode-select {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    font-family: inherit;
    max-width: 110px;
    width: 100%;
}
.ttg-mode-select option { background: #1e293b; color: #fff; }

/* ── Period Label Column ────────────────────────────────────── */
.ttg-period-label {
    background: #f8fafc;
    border-right: 2px solid #e2e8f0;
    border-bottom: 1px solid #f1f5f9;
    padding: 12px 16px;
    vertical-align: top;
    min-width: 130px;
}
.ttg-pname { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.ttg-ptime {
    font-size: 10.5px; color: #475569; font-weight: 600;
    background: #e2e8f0; padding: 2px 7px; border-radius: 4px;
    display: inline-block; margin-bottom: 6px; white-space: nowrap;
}
.ttg-edit-btn {
    background: none; border: none; color: #94a3b8;
    font-size: 11px; cursor: pointer; padding: 2px 4px;
    border-radius: 4px; display: block; margin-top: 2px;
    transition: color 0.2s;
}
.ttg-edit-btn:hover { color: #475569; }

/* ── Data Cells ─────────────────────────────────────────────── */
.ttg-cell {
    border-bottom: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
    padding: 8px;
    vertical-align: top;
    height: 120px;
    position: relative;
}
.ttg-cell-off { background: #f8fafc; }
.ttg-cell-active { background: #fff; }
.ttg-cell.drag-over {
    background: #eff6ff !important;
    outline: 2px dashed #3b82f6;
    outline-offset: -4px;
}

/* ── Subject Card (filled cell) ─────────────────────────────── */
.ttg-subject-card {
    border-radius: 8px;
    padding: 8px 10px 8px 14px;
    min-height: 100px;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    cursor: grab;
    transition: box-shadow 0.2s;
    border-left: 4px solid rgba(0,0,0,0.12);
    overflow: visible;
}
.ttg-subject-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.1); }
.ttg-subject-card.card-dragging { opacity: 0.5; cursor: grabbing; }

.ttg-menu-btn {
    position: absolute; top: 4px; right: 4px;
    background: rgba(255,255,255,0.65); border: none;
    border-radius: 4px; cursor: pointer;
    font-size: 12px; color: #475569;
    padding: 2px 6px; line-height: 1.2;
    font-weight: 700; letter-spacing: 1.5px;
    transition: all 0.2s;
}
.ttg-menu-btn:hover { background: rgba(255,255,255,0.95); }

.ttg-card-subject {
    font-size: 13px; font-weight: 800;
    margin-top: 6px; margin-bottom: 4px;
    line-height: 1.2; padding-right: 20px;
}
.ttg-card-teacher {
    font-size: 11px; font-weight: 600; opacity: 0.7;
    font-style: italic; line-height: 1.3;
}

/* ── Cell Context Dropdown ──────────────────────────────────── */
.ttg-dropdown {
    position: absolute; top: 28px; right: 4px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.13);
    z-index: 300; display: none;
    min-width: 185px; overflow: hidden;
}
.ttg-dropdown.open { display: block; animation: cttFadeIn 0.15s ease; }
.ttg-dropdown button {
    width: 100%; text-align: left; background: none;
    border: none; padding: 9px 14px;
    font-size: 12.5px; font-weight: 600; color: #334155;
    cursor: pointer; display: flex; align-items: center;
    gap: 8px; transition: background 0.15s; font-family: inherit;
}
.ttg-dropdown button:hover { background: #f8fafc; }
.ttg-danger-btn { color: #ef4444 !important; }
.ttg-danger-btn:hover { background: #fef2f2 !important; }

/* ── Empty Cell ─────────────────────────────────────────────── */
.ttg-cell-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 100%; min-height: 100px;
    border: 1.5px dashed #cbd5e1;
    border-radius: 8px;
    color: #94a3b8; font-size: 11.5px;
    font-weight: 600; text-align: center;
    gap: 4px; padding: 6px;
    transition: all 0.2s;
}
.ttg-cell-active:hover .ttg-cell-empty,
.ttg-cell-empty:hover {
    border-color: #93c5fd; background: #f0f9ff; color: #2563eb;
}
.ttg-empty-text { line-height: 1.5; }
.ttg-empty-or { font-size: 10px; color: #e2e8f0; font-weight: 700; }
.ttg-add-btn {
    display: flex; align-items: center; gap: 5px;
    padding: 5px 14px;
    background: #f59e0b; color: #fff;
    border: none; border-radius: 20px;
    font-size: 12px; font-weight: 800;
    cursor: pointer; transition: all 0.2s; font-family: inherit;
}
.ttg-add-btn:hover { background: #d97706; transform: scale(1.04); }

/* ── Inactive Cell ──────────────────────────────────────────── */
.ttg-cell-off-content {
    display: flex; align-items: center; justify-content: center;
    height: 100%; color: #cbd5e1; font-size: 18px;
}

/* ── Drag Hint ──────────────────────────────────────────────── */
.ttg-hint {
    font-size: 12px; color: #2563eb; font-weight: 600;
    padding: 8px 16px 0; font-style: italic;
}
</style>
