@extends('layouts.app')

@section('title', 'Group-Wise Timetable Templates')
@section('page-title', 'Group-Wise Timetable Templates')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════════════════════════
   SHARED MODAL & FORM CSS (required for timetable pages)
   ══════════════════════════════════════════════════════════════ */
.inst-modal {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    padding: 20px;
}
.inst-modal.active {
    display: flex;
    animation: ttFadeIn 0.25s ease;
}
.inst-modal-content {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 580px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.12), 0 10px 10px -5px rgba(0,0,0,0.05);
    border: 1px solid #cbd5e1;
    overflow: hidden;
    animation: ttSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.inst-modal-hdr {
    background: linear-gradient(135deg, #0d2d6e 0%, #1e3a8a 100%);
    padding: 18px 24px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.inst-modal-hdr h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    letter-spacing: -0.5px;
}
.inst-modal-close {
    background: none;
    border: none;
    color: rgba(255,255,255,0.75);
    font-size: 20px;
    cursor: pointer;
    transition: color 0.2s;
    line-height: 1;
    padding: 0 4px;
}
.inst-modal-close:hover { color: #fff; }
.inst-modal-body {
    padding: 24px;
    max-height: 78vh;
    overflow-y: auto;
}
.inst-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}
.inst-form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 16px;
}
.inst-form-label {
    font-size: 12.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.inst-form-control {
    padding: 10px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
    width: 100%;
    background: #fff;
    font-family: inherit;
}
.inst-form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
.inst-form-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 24px;
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
}
.inst-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.inst-table th {
    background: #0f3057;
    color: #fff;
    text-align: left;
    padding: 10px 14px;
    font-weight: 700;
}
.inst-table th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
.inst-table th:last-child  { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
.inst-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 600;
    color: #475569;
}
.inst-table tr:hover td { background: #f8fafc; color: #1e293b; }

@keyframes ttFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
@keyframes ttSlideUp {
    from { transform: translateY(24px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

/* ══════════════════════════════════════════════════════════════
   GROUP-WISE TIMETABLE TEMPLATES — Premium Design System
   ══════════════════════════════════════════════════════════════ */

.tt-container {
    font-family: 'Outfit', sans-serif;
    background: #f8fafc;
    color: #1e293b;
    padding-bottom: 48px;
}

/* Filters & Action Buttons */
.tt-filter-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.tt-academic-year-wrapper {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.tt-academic-year-label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tt-academic-year-select-container {
    position: relative;
    display: flex;
    align-items: center;
}
.tt-academic-year-select-container i {
    position: absolute;
    left: 12px;
    color: #64748b;
    font-size: 14px;
    pointer-events: none;
}
.tt-academic-year-select {
    padding: 10px 36px 10px 36px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: #1e293b;
    outline: none;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 220px;
    appearance: none;
    -webkit-appearance: none;
}
.tt-academic-year-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
.tt-academic-year-select-container::after {
    content: '\f078';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 14px;
    color: #64748b;
    font-size: 10px;
    pointer-events: none;
}

.tt-action-buttons {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* Premium Buttons */
.tt-btn {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
}
.tt-btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}
.tt-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.tt-btn-outline {
    background: transparent;
    color: #2563eb;
    border: 2px solid #2563eb;
    padding: 9px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.tt-btn-outline:hover {
    background: rgba(37, 99, 235, 0.05);
    border-color: #1d4ed8;
    color: #1d4ed8;
    transform: translateY(-2px);
}

/* List Card Row Layout */
.tt-list-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.tt-row-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    display: flex;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.tt-row-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}

.tt-row-left-block {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: #fff;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
    width: 250px;
    min-width: 250px;
    position: relative;
    flex-shrink: 0;
}
.tt-clipboard-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 15px;
    flex-shrink: 0;
}
.tt-group-name {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    flex-grow: 1;
    margin: 0 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.tt-edit-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.tt-edit-circle:hover {
    transform: scale(1.1);
    color: #1d4ed8;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.tt-row-right-block {
    flex-grow: 1;
    display: grid;
    grid-template-columns: 2fr 1.5fr 1fr 1fr 1.2fr;
    gap: 16px;
    padding: 16px 24px;
    align-items: center;
}
.tt-row-col {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.tt-col-label {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tt-col-val {
    font-size: 13.5px;
    font-weight: 600;
    color: #1e293b;
    word-break: break-word;
}

/* Switch styling */
.tt-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}
.tt-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.tt-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ef4444; /* red when inactive */
    transition: .3s;
    border-radius: 24px;
}
.tt-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
input:checked + .tt-slider {
    background-color: #10b981;
}
input:checked + .tt-slider:before {
    transform: translateX(20px);
}

/* Empty State */
.tt-empty {
    background: #fff;
    border-radius: 16px;
    border: 1.5px dashed #cbd5e1;
    padding: 48px;
    text-align: center;
    max-width: 500px;
    margin: 40px auto;
}
.tt-empty-icon {
    font-size: 48px;
    color: #94a3b8;
    margin-bottom: 16px;
}
.tt-empty-title {
    font-size: 18px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 8px;
}
.tt-empty-sub {
    font-size: 13.5px;
    color: #64748b;
    margin-bottom: 20px;
}

@media (max-width: 991px) {
    .tt-row-right-block {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 768px) {
    .tt-row-card {
        flex-direction: column;
    }
    .tt-row-left-block {
        width: 100%;
        border-bottom-left-radius: 0;
        border-top-right-radius: 12px;
    }
}
</style>
</style>
@endsection

@section('content')
<div class="tt-container">
    
    {{-- Header Section --}}
    <div class="tt-filter-section">
        <form id="sessionForm" method="GET" action="{{ route('school.timetable.group') }}">
            <div class="tt-academic-year-wrapper">
                <span class="tt-academic-year-label">Academic Year *</span>
                <div class="tt-academic-year-select-container">
                    <i class="far fa-calendar-alt"></i>
                    <select name="academic_session_id" class="tt-academic-year-select" onchange="document.getElementById('sessionForm').submit()">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $sessionId == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="tt-action-buttons">
            <a href="{{ route('school.timetable.class') }}" class="tt-btn-outline">
                <i class="fas fa-plus"></i> CREATE BULK TIME TABLE
            </a>
            <button class="tt-btn tt-btn-primary" onclick="openWizardModal()">
                <i class="fas fa-plus"></i> CREATE NEW GROUP
            </button>
        </div>
    </div>

    {{-- Templates List --}}
    @if($groups->isEmpty())
        <div class="tt-empty">
            <div class="tt-empty-icon"><i class="fas fa-calendar-week"></i></div>
            <h3 class="tt-empty-title">No Timetable Templates</h3>
            <p class="tt-empty-sub">Create reusable weekly timetable groups templates specifying class timings, days, and period structures for classes.</p>
            <button class="tt-btn tt-btn-primary" onclick="openWizardModal()">
                <i class="fas fa-plus"></i> Create Template Group
            </button>
        </div>
    @else
        <div class="card" style="border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); border-radius: 12px; overflow: hidden; background: #fff;">
            <div class="card-hdr" style="padding: 20px; border-bottom: 1px solid #f1f5f9; background: #fff;">
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.3px;">Group Wise Timetable List</h3>
            </div>
            <div class="card-body" style="padding: 20px; background: #f8fafc;">
                <div class="tt-list-container">
                    @foreach($groups as $group)
                        <div class="tt-row-card" id="group-card-{{ $group->id }}">
                            <div class="tt-row-left-block">
                                <div class="tt-clipboard-circle">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="tt-group-name" title="{{ $group->group_name }}">
                                    {{ $group->group_name }}
                                </div>
                                <button class="tt-edit-circle" data-group="{{ json_encode($group) }}" onclick="openEditWizardModal(JSON.parse(this.getAttribute('data-group')))" title="Edit Template">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>

                            <div class="tt-row-right-block">
                                <div class="tt-row-col">
                                    <span class="tt-col-label">Classes</span>
                                    <span class="tt-col-val">
                                        @php
                                            $uniquePivots = DB::table('timetable_group_class_section')
                                                ->where('timetable_group_id', $group->id)
                                                ->get();
                                            $classNames = [];
                                            foreach($uniquePivots as $pivot) {
                                                $c = App\Models\SchoolClass::find($pivot->class_id);
                                                $s = App\Models\Section::find($pivot->section_id);
                                                if($c && $s) {
                                                    $classNames[] = $c->name . '-' . $s->name;
                                                }
                                            }
                                        @endphp
                                        @if(count($classNames) > 0)
                                            {{ implode(', ', $classNames) }}
                                        @else
                                            <span style="color: #94a3b8; font-style: italic; font-size: 12px;">No classes linked yet.</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="tt-row-col">
                                    <span class="tt-col-label">Start & End Time</span>
                                    <span class="tt-col-val">
                                        {{ date('g:i A', strtotime($group->periods->first()?->start_time ?? $group->class_start_time)) }} to {{ date('g:i A', strtotime($group->periods->last()?->end_time ?? $group->class_start_time)) }}
                                    </span>
                                </div>

                                <div class="tt-row-col">
                                    <span class="tt-col-label">Number of periods</span>
                                    <span class="tt-col-val" style="cursor: pointer; display: inline-flex; align-items: center; gap: 4px; color: #2563eb; font-weight: 700;" onclick="openDetailsModal({{ json_encode($group->periods) }})" title="Click to view Periods breakdown">
                                        {{ $group->periods->count() }} {{ $group->periods->count() == 1 ? 'Period' : 'Periods' }}
                                        <i class="fas fa-info-circle" style="font-size: 11px;"></i>
                                    </span>
                                </div>

                                <div class="tt-row-col">
                                    <span class="tt-col-label">No. of Breaks</span>
                                    <span class="tt-col-val">
                                        @php
                                            $breakCount = 0;
                                            foreach($group->periods as $p) {
                                                $pNameLower = strtolower($p->period_name);
                                                if (str_contains($pNameLower, 'break') || str_contains($pNameLower, 'interval')) {
                                                    $breakCount++;
                                                }
                                            }
                                        @endphp
                                        {{ $breakCount }} {{ $breakCount == 1 ? 'Break' : 'Breaks' }}
                                    </span>
                                </div>

                                <div class="tt-row-col">
                                    <span class="tt-col-label">Current Status</span>
                                    <div class="tt-col-val" style="display: flex; align-items: center; gap: 8px;">
                                        <label class="tt-switch">
                                            <input type="checkbox" {{ $group->is_active ? 'checked' : '' }} onchange="toggleGroupStatus({{ $group->id }})">
                                            <span class="tt-slider"></span>
                                        </label>
                                        <span class="status-label-{{ $group->id }}" style="font-weight: 700; color: {{ $group->is_active ? '#10b981' : '#ef4444' }};">
                                            {{ $group->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Dynamic Modal Inclusions --}}
@include('school.timetable.groups._create-group-modal')


{{-- Modal 2: Periods Schema Modal --}}
<div class="inst-modal" id="modal-periods-schema">
    <div class="inst-modal-content" style="max-width: 500px;">
        <div class="inst-modal-hdr">
            <h3>Periods Breakdown</h3>
            <button class="inst-modal-close" onclick="closeInstModal('modal-periods-schema')">&times;</button>
        </div>
        <div class="inst-modal-body" style="padding: 20px;">
            <table class="inst-table">
                <thead>
                    <tr>
                        <th>Period Name</th>
                        <th>Duration</th>
                        <th>Timings</th>
                    </tr>
                </thead>
                <tbody id="periods-schema-tbody">
                    {{-- populated dynamically --}}
                </tbody>
            </table>
            <div style="display:flex; justify-content:flex-end; margin-top:20px;">
                <button class="btn btn-primary" onclick="closeInstModal('modal-periods-schema')" style="padding: 8px 16px; border-radius: 8px; background:#2563eb; border-color:#2563eb; color:#fff;">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openInstModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeInstModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // Base URL for timetable groups
    const groupBaseUrl = '/school/timetable/groups';

    // Toggle Group is_active
    function toggleGroupStatus(groupId) {
        const toggle = event.target;
        fetch(`${groupBaseUrl}/${groupId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show subtle success toast instead of alert
                showToast(data.message, 'success');
                // Update status label
                const statusLabel = document.querySelector(`.status-label-${groupId}`);
                if (statusLabel) {
                    if (toggle.checked) {
                        statusLabel.textContent = 'Active';
                        statusLabel.style.color = '#10b981';
                    } else {
                        statusLabel.textContent = 'Inactive';
                        statusLabel.style.color = '#ef4444';
                    }
                }
            } else {
                toggle.checked = !toggle.checked; // revert toggle
                showToast('Failed to update status.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toggle.checked = !toggle.checked; // revert toggle
            showToast('Something went wrong.', 'error');
        });
    }



    // Open details periods schema
    function openDetailsModal(periods) {
        const tbody = document.getElementById('periods-schema-tbody');
        tbody.innerHTML = '';
        
        periods.forEach(p => {
            // format times
            const formatTime = (timeStr) => {
                const parts = timeStr.split(':');
                let h = parseInt(parts[0]);
                const m = parts[1];
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12; // 0 should be 12
                return h + ':' + m + ' ' + ampm;
            };

            const row = `
                <tr>
                    <td><strong>${p.period_name}</strong></td>
                    <td>${p.duration_minutes} mins</td>
                    <td>${formatTime(p.start_time)} - ${formatTime(p.end_time)}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        openInstModal('modal-periods-schema');
    }

    // Toast notification helper
    function showToast(message, type) {
        const existingToast = document.getElementById('tt-toast-notif');
        if (existingToast) existingToast.remove();

        const colors = {
            success: { bg: '#10b981', icon: '✓' },
            error: { bg: '#ef4444', icon: '✕' }
        };
        const c = colors[type] || colors.success;

        const toast = document.createElement('div');
        toast.id = 'tt-toast-notif';
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: ${c.bg}; color: #fff;
            padding: 12px 20px; border-radius: 10px;
            font-weight: 700; font-size: 13.5px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            display: flex; align-items: center; gap: 10px;
            animation: slideInRight 0.3s ease;
            font-family: 'Outfit', sans-serif;
        `;
        toast.innerHTML = `<span style="font-size:16px;">${c.icon}</span><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.5s'; setTimeout(() => toast.remove(), 500); }, 3000);
    }
</script>
<style>
@keyframes slideInRight {
    from { transform: translateX(100px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>
@endsection
