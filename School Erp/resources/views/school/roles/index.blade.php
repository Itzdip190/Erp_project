@extends('layouts.app')

@section('title', 'Role Category')
@section('page-title', 'Role Category')

@section('styles')
<style>
/* ── Blue-white theme overrides for this page ── */
:root {
    --rc-blue: #1d4ed8;
    --rc-blue-light: #3b82f6;
    --rc-blue-xlight: #eff6ff;
    --rc-blue-border: #bfdbfe;
    --rc-white: #fff;
    --rc-text-dark: #1e3a5f;
    --rc-text-muted: #64748b;
    --rc-row-alt: #f8faff;
}

.rc-page-header {
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 60%, #60a5fa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 32px rgba(29,78,216,.25);
}
.rc-page-header h1 {
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}
.rc-page-header p { color: rgba(255,255,255,.75); font-size: 13px; }

.rc-access-btn {
    background: rgba(255,255,255,.15);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,.4);
    border-radius: 10px;
    padding: 11px 22px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: .2s;
    text-decoration: none;
    white-space: nowrap;
}
.rc-access-btn:hover { background: rgba(255,255,255,.28); transform: translateY(-1px); color: #fff; }

/* Search bar styling */
.rc-search-card {
    background: #fff;
    border: 1px solid var(--rc-blue-border);
    border-radius: 14px;
    padding: 18px 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(29,78,216,.04);
}
.rc-search-form {
    display: flex;
    gap: 12px;
    align-items: center;
}
.rc-search-input-wrapper {
    position: relative;
    flex: 1;
}
.rc-search-input-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--rc-text-muted);
    font-size: 14px;
}
.rc-search-input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 1.5px solid var(--rc-blue-border);
    border-radius: 10px;
    font-size: 13.5px;
    color: var(--rc-text-dark);
    outline: none;
    transition: all .2s;
}
.rc-search-input:focus {
    border-color: var(--rc-blue-light);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.rc-search-btn {
    background: var(--rc-blue);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
}
.rc-search-btn:hover { background: #1e40af; }
.rc-clear-btn {
    background: #f1f5f9;
    color: var(--rc-text-muted);
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: .2s;
}
.rc-clear-btn:hover { background: #e2e8f0; color: var(--rc-text-dark); }

/* Staff table card */
.rc-table-card {
    background: #white;
    border: 1px solid var(--rc-blue-border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(29,78,216,.06);
}
.rc-table { width: 100%; border-collapse: collapse; }
.rc-table th {
    padding: 14px 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--rc-text-muted);
    background: #f8faff;
    border-bottom: 1.5px solid var(--rc-blue-border);
    text-align: left;
}
.rc-table td {
    padding: 16px 20px;
    font-size: 13px;
    color: var(--rc-text-dark);
    border-bottom: 1px solid #f0f5ff;
    vertical-align: middle;
}
.rc-table tr:last-child td { border-bottom: none; }
.rc-table tr:nth-child(even) td { background: var(--rc-row-alt); }
.rc-table tr:hover td { background: #eff6ff; }

/* Avatar layout */
.staff-avatar-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}
.staff-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--rc-blue) 0%, var(--rc-blue-light) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(29,78,216,.2);
}
.staff-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.staff-name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    color: var(--rc-text-dark);
}
.staff-sub {
    font-size: 11.5px;
    color: var(--rc-text-muted);
    margin-top: 2px;
}

/* Badge tags */
.desg-badge-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.desg-badge {
    background: var(--rc-blue-xlight);
    color: var(--rc-blue);
    border: 1px solid var(--rc-blue-border);
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
}
.role-badge {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 5px;
    text-transform: uppercase;
}
.no-badges {
    color: var(--rc-text-muted);
    font-style: italic;
    font-size: 12px;
}

.action-btn {
    background: var(--rc-blue-xlight);
    color: var(--rc-blue);
    border: 1.5px solid var(--rc-blue-border);
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.action-btn:hover {
    background: var(--rc-blue);
    color: #fff;
    border-color: var(--rc-blue);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(29,78,216,.2);
}

/* ── SLIDE-IN PANEL ── */
.sa-panel-backdrop {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.45);
    z-index: 990;
    display: none;
    backdrop-filter: blur(3px);
}
.sa-panel-backdrop.open { display: block; }

.sa-panel {
    position: fixed; top: 0; right: -460px;
    width: 440px; height: 100vh;
    background: #fff;
    z-index: 1000;
    box-shadow: -8px 0 40px rgba(29,78,216,.18);
    display: flex; flex-direction: column;
    transition: right .35s cubic-bezier(.4,0,.2,1);
    border-left: 1px solid var(--rc-blue-border);
}
.sa-panel.open { right: 0; }

.sa-panel-header {
    padding: 22px 24px 16px;
    background: linear-gradient(135deg, var(--rc-blue) 0%, var(--rc-blue-light) 100%);
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-shrink: 0;
}
.sa-panel-header h3 {
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 16px; font-weight: 800;
    margin-bottom: 3px;
}
.sa-panel-header p { color: rgba(255,255,255,.75); font-size: 11.5px; }
.sa-panel-close {
    background: rgba(255,255,255,.15); border: none;
    color: #fff; width: 30px; height: 30px;
    border-radius: 8px; cursor: pointer; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: .2s; flex-shrink: 0;
}
.sa-panel-close:hover { background: rgba(255,255,255,.25); }

.sa-panel-search-wrap {
    padding: 14px 20px;
    border-bottom: 1px solid var(--rc-blue-border);
    flex-shrink: 0;
}
.sa-panel-search {
    width: 100%; padding: 8px 14px 8px 36px;
    border: 1.5px solid var(--rc-blue-border);
    border-radius: 9px; font-size: 13px;
    outline: none; transition: .2s;
    font-family: 'Inter', sans-serif;
}
.sa-panel-search:focus { border-color: var(--rc-blue-light); box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.sa-search-icon {
    position: absolute; left: 32px; top: 50%;
    transform: translateY(-50%);
    color: var(--rc-text-muted); font-size: 13px;
    pointer-events: none;
}

.sa-panel-list {
    flex: 1; overflow-y: auto; padding: 8px 0;
}
.sa-panel-list::-webkit-scrollbar { width: 4px; }
.sa-panel-list::-webkit-scrollbar-thumb { background: var(--rc-blue-border); border-radius: 4px; }

/* Designation item inside panel */
.desg-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    cursor: pointer;
    transition: background .15s;
    border-bottom: 1px solid #f8faff;
}
.desg-item:hover { background: var(--rc-blue-xlight); }
.desg-item.selected { background: #dbeafe; }
.desg-info { display: flex; flex-direction: column; gap: 3px; }
.desg-name { font-size: 13px; font-weight: 600; color: var(--rc-text-dark); }
.desg-desc { font-size: 11px; color: var(--rc-text-muted); }
.desg-check {
    width: 20px; height: 20px;
    border-radius: 5px; border: 2px solid var(--rc-blue-border);
    display: flex; align-items: center; justify-content: center;
    transition: all .15s; flex-shrink: 0;
    background: #fff;
}
.desg-item.selected .desg-check {
    background: var(--rc-blue);
    border-color: var(--rc-blue);
    color: #fff;
}

.sa-panel-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--rc-blue-border);
    display: flex; gap: 10px; align-items: center;
    flex-shrink: 0;
    background: #fafbff;
}
.sa-panel-save {
    flex: 1; padding: 11px;
    background: var(--rc-blue); color: #fff;
    border: none; border-radius: 9px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; transition: .2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.sa-panel-save:hover { background: #1e40af; }
.sa-panel-cancel {
    padding: 11px 18px;
    background: none; color: var(--rc-text-muted);
    border: 1.5px solid var(--rc-blue-border);
    border-radius: 9px; font-size: 13px;
    font-weight: 600; cursor: pointer; transition: .2s;
}
.sa-panel-cancel:hover { background: var(--rc-blue-xlight); color: var(--rc-blue); }
.sa-panel-sel-count {
    font-size: 11px; color: var(--rc-text-muted);
    text-align: center; margin-top: 4px;
}

/* Empty state */
.sa-empty {
    padding: 40px 20px; text-align: center; color: var(--rc-text-muted);
}
.sa-empty i { font-size: 36px; color: var(--rc-blue-border); margin-bottom: 10px; display: block; }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="rc-page-header">
    <div>
        <h1><i class="fas fa-users-gear" style="margin-right:10px;opacity:.9;"></i>Role Category — Designations</h1>
        <p>Assign multiple designations (roles) to school teachers and staff. Changes automatically synchronize with Spatie permissions.</p>
    </div>
    <a href="{{ route('school.roles.staff-access') }}" class="rc-access-btn">
        <i class="fas fa-shield-halved"></i> Access Control Panel
    </a>
</div>

{{-- Search Card --}}
<div class="rc-search-card">
    <form method="GET" action="{{ route('school.roles.index') }}" class="rc-search-form">
        <div class="rc-search-input-wrapper">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text"
                   name="search"
                   class="rc-search-input"
                   value="{{ $search }}"
                   placeholder="Search staff by name, email, or employee ID...">
        </div>
        <button type="submit" class="rc-search-btn"><i class="fas fa-filter" style="margin-right: 5px;"></i> Filter</button>
        @if($search)
            <a href="{{ route('school.roles.index') }}" class="rc-clear-btn">Clear</a>
        @endif
    </form>
</div>

@if(!$staffList->count())
<div class="sa-empty" style="background:#fff;border-radius:14px;border:1px solid var(--rc-blue-border);">
    <i class="fas fa-user-slash"></i>
    <strong style="display:block;color:#1e3a5f;font-size:15px;margin-bottom:6px;">No Staff Members Found</strong>
    <p>Try refining your search or add new staff from the Staff Directory.</p>
</div>
@else
{{-- Staff Listing --}}
<div class="rc-table-card">
    <table class="rc-table">
        <thead>
            <tr>
                <th style="width: 30%;">Staff Member</th>
                <th style="width: 20%;">Employee ID & Email</th>
                <th style="width: 35%;">Designations (Roles)</th>
                <th style="width: 15%; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffList as $user)
            @php
                $staff = $user->staff;
                $initials = strtoupper(substr($user->name, 0, 1) . (str_contains($user->name, ' ') ? substr($user->name, strrpos($user->name, ' ') + 1, 1) : ''));
            @endphp
            <tr>
                <td>
                    <div class="staff-avatar-wrap">
                        <div class="staff-avatar">
                            @if($staff && $staff->photo && Storage::disk('public')->exists($staff->photo))
                                <img src="{{ Storage::disk('public')->url($staff->photo) }}" alt="{{ $user->name }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <div>
                            <div class="staff-name">{{ $user->name }}</div>
                            <div style="display: flex; gap: 4px; margin-top: 4px;">
                                @foreach($user->roles as $role)
                                    <span class="role-badge">{{ str_replace('_', ' ', $role->name) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-weight: 600;">{{ $staff->employee_id ?? 'N/A' }}</div>
                    <div class="staff-sub">{{ $user->email }}</div>
                </td>
                <td>
                    <div class="desg-badge-wrap" id="staff-desg-badges-{{ $user->id }}">
                        @if($staff && $staff->designations->count())
                            @foreach($staff->designations as $desg)
                                <span class="desg-badge">{{ $desg->name }}</span>
                            @endforeach
                        @else
                            <span class="no-badges">No designation assigned</span>
                        @endif
                    </div>
                </td>
                <td style="text-align: center;">
                    <button type="button"
                            class="action-btn"
                            id="edit-desg-btn-{{ $user->id }}"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-assigned="{{ $staff ? json_encode($staff->designations->pluck('id')) : '[]' }}"
                            onclick="openPanel(this)">
                        <i class="fas fa-user-pen"></i> Assign Roles
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="margin-top: 15px;">
    {{ $staffList->links() }}
</div>
@endif

{{-- ── SLIDE-IN PANEL ── --}}
<div class="sa-panel-backdrop" id="panelBackdrop" onclick="closePanel()"></div>
<div class="sa-panel" id="designationPanel">
    <div class="sa-panel-header">
        <div>
            <h3 id="panelTitle">Assign Designations</h3>
            <p id="panelSubtitle">Manage roles for staff</p>
        </div>
        <button class="sa-panel-close" onclick="closePanel()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sa-panel-search-wrap" style="position:relative;">
        <i class="fas fa-magnifying-glass sa-search-icon"></i>
        <input type="text"
               class="sa-panel-search"
               id="desgSearch"
               placeholder="Search designations..."
               oninput="filterDesignations(this.value)">
    </div>

    <div class="sa-panel-list" id="desgList">
        {{-- Populated by JS --}}
    </div>

    <div class="sa-panel-footer">
        <div style="flex:1;">
            <button class="sa-panel-save" id="panelSaveBtn" onclick="savePanel()">
                <i class="fas fa-check"></i> Save Designations
            </button>
            <div class="sa-panel-sel-count" id="selCount">0 designations selected</div>
        </div>
        <button class="sa-panel-cancel" onclick="closePanel()">Cancel</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
const ALL_DESIGNATIONS = @json($designations);

// Currently open panel context
let panelCtx = { userId: '', userName: '' };
let selectedIds = new Set();

function openPanel(btnEl) {
    const userId = btnEl.getAttribute('data-id');
    const userName = btnEl.getAttribute('data-name');
    const userEmail = btnEl.getAttribute('data-email');
    const assignedIds = JSON.parse(btnEl.getAttribute('data-assigned') || '[]');

    panelCtx = { userId, userName };

    // Set panel titles
    document.getElementById('panelTitle').textContent = userName;
    document.getElementById('panelSubtitle').textContent = `Manage designations for ${userEmail}`;

    // Initialize selections
    selectedIds = new Set(assignedIds);
    renderDesignationsList(ALL_DESIGNATIONS);
    updateSelCount();

    document.getElementById('desgSearch').value = '';
    document.getElementById('panelBackdrop').classList.add('open');
    document.getElementById('designationPanel').classList.add('open');
}

function closePanel() {
    document.getElementById('panelBackdrop').classList.remove('open');
    document.getElementById('designationPanel').classList.remove('open');
}

function renderDesignationsList(desgArr) {
    const list = document.getElementById('desgList');
    if (!desgArr.length) {
        list.innerHTML = '<div class="sa-empty"><i class="fas fa-circle-exclamation"></i>No designations found.</div>';
        return;
    }
    list.innerHTML = desgArr.map(d => `
        <div class="desg-item ${selectedIds.has(d.id) ? 'selected' : ''}"
             onclick="toggleDesignation(${d.id}, this)" data-id="${d.id}">
            <div class="desg-info">
                <span class="desg-name">${d.name}</span>
                <span class="desg-desc">${d.description || 'No description provided'}</span>
            </div>
            <div class="desg-check">
                ${selectedIds.has(d.id) ? '<i class="fas fa-check" style="font-size:10px;"></i>' : ''}
            </div>
        </div>
    `).join('');
}

function toggleDesignation(id, el) {
    if (selectedIds.has(id)) {
        selectedIds.delete(id);
        el.classList.remove('selected');
        el.querySelector('.desg-check').innerHTML = '';
    } else {
        selectedIds.add(id);
        el.classList.add('selected');
        el.querySelector('.desg-check').innerHTML = '<i class="fas fa-check" style="font-size:10px;"></i>';
    }
    updateSelCount();
}

function updateSelCount() {
    const n = selectedIds.size;
    document.getElementById('selCount').textContent = n + ' designation' + (n !== 1 ? 's' : '') + ' selected';
}

function filterDesignations(q) {
    const lower = q.toLowerCase();
    const filtered = ALL_DESIGNATIONS.filter(d =>
        d.name.toLowerCase().includes(lower) || (d.description && d.description.toLowerCase().includes(lower))
    );
    renderDesignationsList(filtered);
}

function savePanel() {
    const btn = document.getElementById('panelSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('{{ route("school.roles.update-staff") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id:         panelCtx.userId,
            designation_ids: [...selectedIds]
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update the badges list on the table
            const badgesWrap = document.getElementById('staff-desg-badges-' + panelCtx.userId);
            if (badgesWrap) {
                if (data.designations && data.designations.length) {
                    badgesWrap.innerHTML = data.designations.map(d => `<span class="desg-badge">${d.name}</span>`).join('');
                } else {
                    badgesWrap.innerHTML = '<span class="no-badges">No designation assigned</span>';
                }
            }

            // Update the Spatie roles on the row
            const editBtn = document.getElementById('edit-desg-btn-' + panelCtx.userId);
            if (editBtn) {
                editBtn.setAttribute('data-assigned', JSON.stringify([...selectedIds]));
                
                // Update the roles badges on the row (next to name)
                const rolesWrap = editBtn.closest('tr').querySelector('.staff-avatar-wrap > div > div:nth-child(2)');
                if (rolesWrap && data.roles) {
                    rolesWrap.innerHTML = data.roles.map(r => `<span class="role-badge">${r.replace('_', ' ')}</span>`).join('');
                }
            }

            closePanel();
            showToast('Designations updated successfully!');
        } else {
            showToast(data.error || 'Failed to save designations.');
        }
    })
    .catch(() => showToast('Error saving designations. Please try again.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Save Designations';
    });
}

function showToast(msg) {
    const toast = document.getElementById('appToast') || document.createElement('div');
    if (!toast.id) {
        toast.id = 'appToast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}
</script>
@endsection
