@extends('layouts.app')

@section('title', 'Staff Access Control')
@section('page-title', 'Staff Access Control')

@section('styles')
<style>
:root{
    --sa-blue:#1d4ed8;
    --sa-blue2:#3b82f6;
    --sa-blue3:#60a5fa;
    --sa-xlight:#eff6ff;
    --sa-border:#bfdbfe;
    --sa-text:#1e3a5f;
    --sa-muted:#64748b;
}

/* Page header */
.sa-header{
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 60%, #60a5fa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 32px rgba(29,78,216,.25);
}
.sa-header h1{
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}
.sa-header p{ color: rgba(255,255,255,.75); font-size: 13px; }
.sa-info-badge{
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 10px;
    padding: 12px 18px;
    color: #fff;
    font-size: 12.5px;
    max-width: 280px;
    line-height: 1.6;
}
.sa-info-badge i{ color: #fcd34d; }

/* Module card */
.sa-module{
    background: #fff;
    border: 1px solid var(--sa-border);
    border-radius: 14px;
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(29,78,216,.06);
    transition: box-shadow .2s;
}
.sa-module:hover{ box-shadow: 0 4px 20px rgba(29,78,216,.1); }
.sa-module-hdr{
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 14px 22px;
    background: linear-gradient(90deg, var(--sa-xlight) 0%, #fff 100%);
    border-bottom: 1px solid var(--sa-border);
    cursor: pointer;
    user-select: none;
    transition: background .2s;
}
.sa-module-hdr:hover{ background: linear-gradient(90deg, #dbeafe 0%, #f0f9ff 100%); }
.sa-mod-icon{
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, var(--sa-blue) 0%, var(--sa-blue2) 100%);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 14px; flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(29,78,216,.3);
}
.sa-mod-title{
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px; font-weight: 700;
    color: var(--sa-text); flex: 1;
}
.sa-mod-chevron{ color: var(--sa-blue2); font-size: 11px; transition: transform .3s; }
.sa-module-hdr.open .sa-mod-chevron{ transform: rotate(180deg); }

/* Feature table */
.sa-features{ display: none; overflow-x: auto; }
.sa-features.open{ display: block; }
.sa-table{ width: 100%; border-collapse: collapse; min-width: 600px; }
.sa-table th{
    padding: 10px 16px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--sa-muted);
    background: #f8faff;
    border-bottom: 1.5px solid var(--sa-border);
    text-align: left;
}
.sa-table th.center{ text-align: center; }
.sa-table td{
    padding: 10px 16px;
    font-size: 13px; color: var(--sa-text);
    border-bottom: 1px solid #f0f5ff;
    vertical-align: middle;
}
.sa-table tr:last-child td{ border-bottom: none; }
.sa-table tr:nth-child(even) td{ background: #f8faff; }
.sa-table tr:hover td{ background: #eff6ff; }

.sa-feat-name{
    font-weight: 500;
    display: flex; align-items: center; gap: 8px;
}
.sa-feat-name::before{
    content: ''; width: 5px; height: 5px; border-radius: 50%;
    background: var(--sa-blue2); flex-shrink: 0;
}

/* Clickable access cells */
.sa-access-cell{
    text-align: center;
}
.sa-access-btn{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    border: 1.5px solid;
    transition: all .2s;
    min-width: 90px;
    position: relative;
}
/* View button - light blue */
.sa-access-btn.view-btn{
    background: var(--sa-xlight);
    color: var(--sa-blue2);
    border-color: var(--sa-border);
}
.sa-access-btn.view-btn:hover{
    background: #dbeafe;
    border-color: var(--sa-blue2);
    transform: translateY(-1px);
}
.sa-access-btn.view-btn.has-access{
    background: #dbeafe;
    color: var(--sa-blue);
    border-color: var(--sa-blue2);
}
/* Edit button - dark blue */
.sa-access-btn.edit-btn{
    background: var(--sa-xlight);
    color: var(--sa-blue);
    border-color: var(--sa-border);
}
.sa-access-btn.edit-btn:hover{
    background: #bfdbfe;
    border-color: var(--sa-blue);
    transform: translateY(-1px);
}
.sa-access-btn.edit-btn.has-access{
    background: var(--sa-blue);
    color: #fff;
    border-color: var(--sa-blue);
}
.sa-badge-count{
    background: rgba(29,78,216,.15);
    color: var(--sa-blue);
    border-radius: 10px;
    font-size: 9.5px;
    padding: 1px 5px;
    font-weight: 800;
}
.sa-access-btn.edit-btn.has-access .sa-badge-count{
    background: rgba(255,255,255,.25);
    color: #fff;
}

/* ── SLIDE-IN PANEL ── */
.sa-panel-backdrop{
    position: fixed; inset: 0;
    background: rgba(15,23,42,.45);
    z-index: 990;
    display: none;
    backdrop-filter: blur(3px);
}
.sa-panel-backdrop.open{ display: block; }

.sa-panel{
    position: fixed; top: 0; right: -460px;
    width: 440px; height: 100vh;
    background: #fff;
    z-index: 1000;
    box-shadow: -8px 0 40px rgba(29,78,216,.18);
    display: flex; flex-direction: column;
    transition: right .35s cubic-bezier(.4,0,.2,1);
    border-left: 1px solid var(--sa-border);
}
.sa-panel.open{ right: 0; }

.sa-panel-header{
    padding: 22px 24px 16px;
    background: linear-gradient(135deg, var(--sa-blue) 0%, var(--sa-blue2) 100%);
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-shrink: 0;
}
.sa-panel-header h3{
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 16px; font-weight: 800;
    margin-bottom: 3px;
}
.sa-panel-header p{ color: rgba(255,255,255,.75); font-size: 11.5px; }
.sa-panel-close{
    background: rgba(255,255,255,.15); border: none;
    color: #fff; width: 30px; height: 30px;
    border-radius: 8px; cursor: pointer; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: .2s; flex-shrink: 0;
}
.sa-panel-close:hover{ background: rgba(255,255,255,.25); }

.sa-panel-search-wrap{
    padding: 14px 20px;
    border-bottom: 1px solid var(--sa-border);
    flex-shrink: 0;
}
.sa-panel-search{
    width: 100%; padding: 8px 14px 8px 36px;
    border: 1.5px solid var(--sa-border);
    border-radius: 9px; font-size: 13px;
    outline: none; transition: .2s;
    font-family: 'Inter', sans-serif;
}
.sa-panel-search:focus{ border-color: var(--sa-blue2); box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.sa-search-icon{
    position: absolute; left: 32px; top: 50%;
    transform: translateY(-50%);
    color: var(--sa-muted); font-size: 13px;
    pointer-events: none;
}

.sa-panel-list{
    flex: 1; overflow-y: auto; padding: 8px 0;
}
.sa-panel-list::-webkit-scrollbar{ width: 4px; }
.sa-panel-list::-webkit-scrollbar-thumb{ background: var(--sa-border); border-radius: 4px; }

.sa-staff-item{
    display: flex; align-items: center; gap: 12px;
    padding: 10px 20px; cursor: pointer;
    transition: background .15s; border-bottom: 1px solid #f8faff;
}
.sa-staff-item:hover{ background: var(--sa-xlight); }
.sa-staff-item.selected{ background: #dbeafe; }

.sa-staff-avatar{
    width: 36px; height: 36px; border-radius: 9px;
    background: linear-gradient(135deg, var(--sa-blue) 0%, var(--sa-blue2) 100%);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.sa-staff-name{ font-size: 13px; font-weight: 600; color: var(--sa-text); }
.sa-staff-role{ font-size: 11px; color: var(--sa-muted); margin-top: 1px; }
.sa-staff-check{
    margin-left: auto; width: 20px; height: 20px;
    border-radius: 5px; border: 2px solid var(--sa-border);
    display: flex; align-items: center; justify-content: center;
    transition: all .15s; flex-shrink: 0;
    background: #fff;
}
.sa-staff-item.selected .sa-staff-check{
    background: var(--sa-blue); border-color: var(--sa-blue);
    color: #fff;
}

.sa-panel-footer{
    padding: 16px 20px;
    border-top: 1px solid var(--sa-border);
    display: flex; gap: 10px; align-items: center;
    flex-shrink: 0;
    background: #fafbff;
}
.sa-panel-save{
    flex: 1; padding: 11px;
    background: var(--sa-blue); color: #fff;
    border: none; border-radius: 9px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; transition: .2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.sa-panel-save:hover{ background: #1e40af; }
.sa-panel-cancel{
    padding: 11px 18px;
    background: none; color: var(--sa-muted);
    border: 1.5px solid var(--sa-border);
    border-radius: 9px; font-size: 13px;
    font-weight: 600; cursor: pointer; transition: .2s;
}
.sa-panel-cancel:hover{ background: var(--sa-xlight); color: var(--sa-blue); }
.sa-panel-sel-count{
    font-size: 11px; color: var(--sa-muted);
    text-align: center; margin-top: 4px;
}

/* Empty state */
.sa-empty{
    padding: 40px 20px; text-align: center; color: var(--sa-muted);
}
.sa-empty i{ font-size: 36px; color: var(--sa-border); margin-bottom: 10px; display: block; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="sa-header">
    <div>
        <h1><i class="fas fa-users-gear" style="margin-right:10px;opacity:.9;"></i>Staff Access Control</h1>
        <p>Click on any View or Edit cell to select which staff members get that access.</p>
    </div>
    <div class="sa-info-badge">
        <i class="fas fa-lightbulb"></i>
        <strong> How it works:</strong><br>
        Click any <em>View</em> or <em>Edit</em> button — a panel slides in showing all staff. Select who should have access and save. Granted staff can see that module in their dashboard.
    </div>
</div>

@if(!$staff->count())
<div class="sa-empty" style="background:#fff;border-radius:14px;border:1px solid var(--sa-border);">
    <i class="fas fa-user-slash"></i>
    <strong style="display:block;color:#1e3a5f;font-size:15px;margin-bottom:6px;">No Teaching Staff Found</strong>
    <p>Add staff members with Teacher, Accountant or School Admin roles first.</p>
</div>
@else

{{-- Module accordion list --}}
@foreach($modules as $moduleKey => $module)
<div class="sa-module">
    <div class="sa-module-hdr" onclick="this.classList.toggle('open'); this.nextElementSibling.classList.toggle('open')">
        <div class="sa-mod-icon"><i class="fas {{ $module['icon'] }}"></i></div>
        <div class="sa-mod-title">{{ $module['label'] }}</div>
        <i class="fas fa-chevron-down sa-mod-chevron"></i>
    </div>

    <div class="sa-features" id="sa-feat-{{ $moduleKey }}">
        <table class="sa-table">
            <thead>
                <tr>
                    <th style="width:50%">Feature / Sub-Module</th>
                    <th class="center" style="width:25%">
                        <i class="fas fa-eye" style="color:#60a5fa;margin-right:5px;"></i>View Access
                    </th>
                    <th class="center" style="width:25%">
                        <i class="fas fa-pen" style="color:#1d4ed8;margin-right:5px;"></i>Edit Access
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($module['features'] as $featureKey => $featureLabel)
                @php
                    // Count how many staff have view/edit for this feature
                    $viewCount = 0; $editCount = 0;
                    foreach($staff as $s) {
                        $row = $access->get("{$s->id}.{$moduleKey}.{$featureKey}");
                        if ($row?->view_access) $viewCount++;
                        if ($row?->edit_access) $editCount++;
                    }
                @endphp
                <tr>
                    <td>
                        <span class="sa-feat-name">{{ $featureLabel }}</span>
                    </td>
                    <td class="sa-access-cell">
                        <button type="button"
                            class="sa-access-btn view-btn {{ $viewCount > 0 ? 'has-access' : '' }}"
                            id="view-btn-{{ $moduleKey }}-{{ $featureKey }}"
                            onclick="openPanel('{{ $moduleKey }}', '{{ $featureKey }}', 'view', '{{ $featureLabel }}')"
                            title="Click to manage view access for {{ $featureLabel }}">
                            <i class="fas fa-eye"></i>
                            View
                            <span class="sa-badge-count" id="view-count-{{ $moduleKey }}-{{ $featureKey }}">{{ $viewCount }}</span>
                        </button>
                    </td>
                    <td class="sa-access-cell">
                        <button type="button"
                            class="sa-access-btn edit-btn {{ $editCount > 0 ? 'has-access' : '' }}"
                            id="edit-btn-{{ $moduleKey }}-{{ $featureKey }}"
                            onclick="openPanel('{{ $moduleKey }}', '{{ $featureKey }}', 'edit', '{{ $featureLabel }}')"
                            title="Click to manage edit access for {{ $featureLabel }}">
                            <i class="fas fa-pen"></i>
                            Edit
                            <span class="sa-badge-count" id="edit-count-{{ $moduleKey }}-{{ $featureKey }}">{{ $editCount }}</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endif

{{-- ── SLIDE-IN PANEL ── --}}
<div class="sa-panel-backdrop" id="panelBackdrop" onclick="closePanel()"></div>
<div class="sa-panel" id="staffPanel">
    <div class="sa-panel-header">
        <div>
            <h3 id="panelTitle">Select Staff</h3>
            <p id="panelSubtitle">Choose who gets access</p>
        </div>
        <button class="sa-panel-close" onclick="closePanel()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sa-panel-search-wrap" style="position:relative;">
        <i class="fas fa-magnifying-glass sa-search-icon"></i>
        <input type="text"
               class="sa-panel-search"
               id="staffSearch"
               placeholder="Search staff by name or role..."
               oninput="filterStaff(this.value)">
    </div>

    <div class="sa-panel-list" id="staffList">
        {{-- Populated by JS --}}
    </div>

    <div class="sa-panel-footer">
        <div style="flex:1;">
            <button class="sa-panel-save" id="panelSaveBtn" onclick="savePanel()">
                <i class="fas fa-check"></i> Save Access
            </button>
            <div class="sa-panel-sel-count" id="selCount">0 staff selected</div>
        </div>
        <button class="sa-panel-cancel" onclick="closePanel()">Cancel</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
const ALL_STAFF = @json($staff->map(fn($u) => [
    'id'   => $u->id,
    'name' => $u->name,
    'role' => ucfirst(str_replace('_', ' ', $u->roles->first()?->name ?? 'Staff')),
    'initials' => strtoupper(substr($u->name, 0, 1) . (str_contains($u->name, ' ') ? substr($u->name, strrpos($u->name, ' ') + 1, 1) : ''))
]));

// Currently open panel context
let panelCtx = { moduleKey: '', featureKey: '', accessType: '', featureLabel: '' };
let selectedIds = new Set();
let allStaffForPanel = [];

function openPanel(moduleKey, featureKey, accessType, featureLabel) {
    panelCtx = { moduleKey, featureKey, accessType, featureLabel };

    // Set panel title
    const typeLabel = accessType === 'view' ? 'View Access' : 'Edit Access';
    document.getElementById('panelTitle').textContent = featureLabel;
    document.getElementById('panelSubtitle').textContent = `Select staff who get ${typeLabel}`;

    // Fetch current granted staff from server
    fetch('{{ route("school.roles.staff-cell") }}?' + new URLSearchParams({
        module_key: moduleKey,
        feature_key: featureKey,
        access_type: accessType,
        _token: '{{ csrf_token() }}'
    }))
    .then(r => r.json())
    .then(data => {
        allStaffForPanel = data.staff;
        selectedIds = new Set(data.staff.filter(s => s.granted).map(s => s.id));
        renderStaffList(allStaffForPanel);
        updateSelCount();
    })
    .catch(() => {
        // Fallback: use all staff
        allStaffForPanel = ALL_STAFF.map(s => ({...s, granted: false}));
        selectedIds = new Set();
        renderStaffList(allStaffForPanel);
    });

    document.getElementById('staffSearch').value = '';
    document.getElementById('panelBackdrop').classList.add('open');
    document.getElementById('staffPanel').classList.add('open');
}

function closePanel() {
    document.getElementById('panelBackdrop').classList.remove('open');
    document.getElementById('staffPanel').classList.remove('open');
}

function renderStaffList(staffArr) {
    const list = document.getElementById('staffList');
    if (!staffArr.length) {
        list.innerHTML = '<div class="sa-empty"><i class="fas fa-users-slash"></i>No staff found.</div>';
        return;
    }
    list.innerHTML = staffArr.map(s => `
        <div class="sa-staff-item ${selectedIds.has(s.id) ? 'selected' : ''}"
             onclick="toggleStaff(${s.id}, this)" data-id="${s.id}">
            <div class="sa-staff-avatar">${s.initials || s.name.charAt(0).toUpperCase()}</div>
            <div>
                <div class="sa-staff-name">${s.name}</div>
                <div class="sa-staff-role">${s.role}</div>
            </div>
            <div class="sa-staff-check">
                ${selectedIds.has(s.id) ? '<i class="fas fa-check" style="font-size:10px;"></i>' : ''}
            </div>
        </div>
    `).join('');
}

function toggleStaff(id, el) {
    if (selectedIds.has(id)) {
        selectedIds.delete(id);
        el.classList.remove('selected');
        el.querySelector('.sa-staff-check').innerHTML = '';
    } else {
        selectedIds.add(id);
        el.classList.add('selected');
        el.querySelector('.sa-staff-check').innerHTML = '<i class="fas fa-check" style="font-size:10px;"></i>';
    }
    updateSelCount();
}

function updateSelCount() {
    const n = selectedIds.size;
    document.getElementById('selCount').textContent = n + ' staff selected';
}

function filterStaff(q) {
    const lower = q.toLowerCase();
    const filtered = allStaffForPanel.filter(s =>
        s.name.toLowerCase().includes(lower) || s.role.toLowerCase().includes(lower)
    );
    renderStaffList(filtered);
}

function savePanel() {
    const btn = document.getElementById('panelSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('{{ route("school.roles.staff-cell.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            module_key:  panelCtx.moduleKey,
            feature_key: panelCtx.featureKey,
            access_type: panelCtx.accessType,
            user_ids:    [...selectedIds]
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update button badge on the table
            const count = selectedIds.size;
            const countEl = document.getElementById(
                panelCtx.accessType + '-count-' + panelCtx.moduleKey + '-' + panelCtx.featureKey
            );
            const btnEl = document.getElementById(
                panelCtx.accessType + '-btn-' + panelCtx.moduleKey + '-' + panelCtx.featureKey
            );
            if (countEl) countEl.textContent = count;
            if (btnEl) {
                if (count > 0) btnEl.classList.add('has-access');
                else btnEl.classList.remove('has-access');
            }
            closePanel();
            showToast('Access saved for ' + count + ' staff member' + (count !== 1 ? 's' : '') + '!');
        }
    })
    .catch(() => showToast('Error saving access. Please try again.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Save Access';
    });
}

// Auto-open first module
document.addEventListener('DOMContentLoaded', () => {
    const first = document.querySelector('.sa-module-hdr');
    if (first) {
        first.classList.add('open');
        if (first.nextElementSibling) first.nextElementSibling.classList.add('open');
    }
});
</script>
@endsection
