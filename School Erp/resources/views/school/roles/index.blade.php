@extends('layouts.app')

@section('title', 'Role Category')
@section('page-title', 'Role Category')

@section('styles')
<style>
/* ── Blue-white theme overrides for this page ── */
:root{
    --rc-blue:#1d4ed8;
    --rc-blue-light:#3b82f6;
    --rc-blue-xlight:#eff6ff;
    --rc-blue-border:#bfdbfe;
    --rc-white:#fff;
    --rc-text-dark:#1e3a5f;
    --rc-text-muted:#64748b;
    --rc-row-alt:#f8faff;
}

.rc-page-header{
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 60%, #60a5fa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 32px rgba(29,78,216,.25);
}
.rc-page-header h1{
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}
.rc-page-header p{ color: rgba(255,255,255,.75); font-size: 13px; }
.rc-save-btn{
    background: #fff;
    color: var(--rc-blue);
    border: none;
    border-radius: 10px;
    padding: 11px 28px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: .2s;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
}
.rc-save-btn:hover{ background: #f0f9ff; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }

/* Module accordion card */
.rc-module{
    background: #fff;
    border: 1px solid var(--rc-blue-border);
    border-radius: 14px;
    margin-bottom: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(29,78,216,.06);
    transition: box-shadow .2s;
}
.rc-module:hover{ box-shadow: 0 4px 20px rgba(29,78,216,.1); }

.rc-module-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    background: linear-gradient(90deg, var(--rc-blue-xlight) 0%, #fff 100%);
    border-bottom: 1px solid var(--rc-blue-border);
    cursor: pointer;
    user-select: none;
    transition: background .2s;
}
.rc-module-header:hover{ background: linear-gradient(90deg, #dbeafe 0%, #f0f9ff 100%); }
.rc-module-header-left{ display: flex; align-items: center; gap: 12px; }
.rc-module-icon{
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, var(--rc-blue) 0%, var(--rc-blue-light) 100%);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 14px; flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(29,78,216,.3);
}
.rc-module-title{
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: var(--rc-text-dark);
}
.rc-module-subtitle{ font-size: 11px; color: var(--rc-text-muted); margin-top: 1px; }
.rc-module-right{ display: flex; align-items: center; gap: 12px; }
.rc-module-count{
    background: var(--rc-blue-xlight);
    color: var(--rc-blue);
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid var(--rc-blue-border);
}
.rc-toggle-all{
    background: none; border: 1.5px solid var(--rc-blue-border);
    color: var(--rc-blue); border-radius: 6px;
    padding: 4px 10px; font-size: 11px; font-weight: 600;
    cursor: pointer; transition: .15s;
}
.rc-toggle-all:hover{ background: var(--rc-blue-xlight); }
.rc-chevron{
    color: var(--rc-blue-light); font-size: 11px;
    transition: transform .3s;
}
.rc-module-header.open .rc-chevron{ transform: rotate(180deg); }

/* Feature table inside accordion */
.rc-features{ display: none; }
.rc-features.open{ display: block; }
.rc-feature-table{ width: 100%; border-collapse: collapse; }
.rc-feature-table th{
    padding: 10px 20px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--rc-text-muted);
    background: #f8faff;
    border-bottom: 1.5px solid var(--rc-blue-border);
    text-align: left;
}
.rc-feature-table th.center{ text-align: center; }
.rc-feature-table td{
    padding: 11px 20px;
    font-size: 13px;
    color: var(--rc-text-dark);
    border-bottom: 1px solid #f0f5ff;
    vertical-align: middle;
}
.rc-feature-table tr:last-child td{ border-bottom: none; }
.rc-feature-table tr:nth-child(even) td{ background: var(--rc-row-alt); }
.rc-feature-table tr:hover td{ background: #eff6ff; }

.feature-name{ font-weight: 500; display: flex; align-items: center; gap: 8px; }
.feature-name::before{
    content: '';
    width: 5px; height: 5px; border-radius: 50%;
    background: var(--rc-blue-light); flex-shrink: 0;
}

/* Toggle switches */
.toggle-cell{ text-align: center; }
.toggle-wrap{ display: flex; align-items: center; justify-content: center; gap: 6px; }
.toggle-label{ font-size: 10.5px; color: var(--rc-text-muted); font-weight: 600; }

.rc-switch{
    position: relative; display: inline-block;
    width: 42px; height: 22px;
}
.rc-switch input{ opacity: 0; width: 0; height: 0; }
.rc-slider{
    position: absolute; cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #cbd5e1;
    border-radius: 22px;
    transition: .25s;
}
.rc-slider::before{
    content: ''; position: absolute;
    height: 16px; width: 16px;
    left: 3px; bottom: 3px;
    background: white; border-radius: 50%;
    transition: .25s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.rc-switch input:checked + .rc-slider{ background: var(--rc-blue); }
.rc-switch input:checked + .rc-slider::before{ transform: translateX(20px); }

/* View toggle: lighter blue */
.rc-switch.view-toggle input:checked + .rc-slider{ background: #60a5fa; }

/* Summary stats bar */
.rc-stats{
    display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
}
.rc-stat{
    flex: 1; min-width: 120px;
    background: #fff;
    border: 1px solid var(--rc-blue-border);
    border-radius: 12px;
    padding: 14px 18px;
    text-align: center;
}
.rc-stat-num{
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 24px; font-weight: 800;
    color: var(--rc-blue);
}
.rc-stat-label{ font-size: 11px; color: var(--rc-text-muted); margin-top: 2px; }
</style>
@endsection

@section('content')

<form method="POST" action="{{ route('school.roles.permissions.update') }}" id="rolePermForm">
@csrf
@method('PUT')

{{-- Page Header --}}
<div class="rc-page-header">
    <div>
        <h1><i class="fas fa-shield-halved" style="margin-right:10px;opacity:.9;"></i>Role Category — Module Permissions</h1>
        <p>Toggle View & Edit access for each module feature. Staff access is configured separately in Staff Access Control.</p>
    </div>
    <button type="submit" class="rc-save-btn">
        <i class="fas fa-floppy-disk"></i> Save All Permissions
    </button>
</div>

{{-- Stats bar --}}
<div class="rc-stats" id="rcStats">
    <div class="rc-stat">
        <div class="rc-stat-num" id="totalModules">{{ count($modules) }}</div>
        <div class="rc-stat-label">Total Modules</div>
    </div>
    <div class="rc-stat">
        <div class="rc-stat-num" id="totalFeatures">{{ collect($modules)->sum(fn($m) => count($m['features'])) }}</div>
        <div class="rc-stat-label">Total Features</div>
    </div>
    <div class="rc-stat">
        <div class="rc-stat-num" id="viewEnabled">0</div>
        <div class="rc-stat-label">View Enabled</div>
    </div>
    <div class="rc-stat">
        <div class="rc-stat-num" id="editEnabled">0</div>
        <div class="rc-stat-label">Edit Enabled</div>
    </div>
</div>

{{-- Module accordion list --}}
@foreach($modules as $moduleKey => $module)
@php $featureCount = count($module['features']); @endphp
<div class="rc-module">
    {{-- Module Header --}}
    <div class="rc-module-header" onclick="toggleModule(this)">
        <div class="rc-module-header-left">
            <div class="rc-module-icon">
                <i class="fas {{ $module['icon'] }}"></i>
            </div>
            <div>
                <div class="rc-module-title">{{ $module['label'] }}</div>
                <div class="rc-module-subtitle">{{ $featureCount }} feature{{ $featureCount !== 1 ? 's' : '' }}</div>
            </div>
        </div>
        <div class="rc-module-right">
            <span class="rc-module-count" id="count-{{ $moduleKey }}">0 / {{ $featureCount }}</span>
            <button type="button" class="rc-toggle-all"
                    onclick="event.stopPropagation(); toggleAllModule('{{ $moduleKey }}', this)">
                Enable All
            </button>
            <i class="fas fa-chevron-down rc-chevron"></i>
        </div>
    </div>

    {{-- Feature Table --}}
    <div class="rc-features" id="features-{{ $moduleKey }}">
        <table class="rc-feature-table">
            <thead>
                <tr>
                    <th style="width:55%">Feature / Sub-Module</th>
                    <th class="center" style="width:22.5%"><i class="fas fa-eye" style="color:#60a5fa;margin-right:5px;"></i>View Access</th>
                    <th class="center" style="width:22.5%"><i class="fas fa-pen" style="color:#1d4ed8;margin-right:5px;"></i>Edit Access</th>
                </tr>
            </thead>
            <tbody>
                @foreach($module['features'] as $featureKey => $featureLabel)
                @php
                    $savedRow   = $saved->get("{$moduleKey}.{$featureKey}");
                    $viewChecked = $savedRow?->view_access ?? false;
                    $editChecked = $savedRow?->edit_access ?? false;
                    $viewName   = "view_{$moduleKey}_{$featureKey}";
                    $editName   = "edit_{$moduleKey}_{$featureKey}";
                @endphp
                <tr>
                    <td>
                        <span class="feature-name">{{ $featureLabel }}</span>
                    </td>
                    <td class="toggle-cell">
                        <div class="toggle-wrap">
                            <span class="toggle-label">OFF</span>
                            <label class="rc-switch view-toggle">
                                <input type="checkbox"
                                       name="{{ $viewName }}"
                                       value="1"
                                       {{ $viewChecked ? 'checked' : '' }}
                                       class="view-chk mod-chk-{{ $moduleKey }}"
                                       onchange="updateStats(); updateModuleCount('{{ $moduleKey }}')">
                                <span class="rc-slider"></span>
                            </label>
                            <span class="toggle-label">ON</span>
                        </div>
                    </td>
                    <td class="toggle-cell">
                        <div class="toggle-wrap">
                            <span class="toggle-label">OFF</span>
                            <label class="rc-switch edit-toggle">
                                <input type="checkbox"
                                       name="{{ $editName }}"
                                       value="1"
                                       {{ $editChecked ? 'checked' : '' }}
                                       class="edit-chk mod-chk-{{ $moduleKey }}"
                                       onchange="updateStats(); updateModuleCount('{{ $moduleKey }}')">
                                <span class="rc-slider"></span>
                            </label>
                            <span class="toggle-label">ON</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- Bottom Save --}}
<div style="text-align:right; margin-top:8px;">
    <button type="submit" class="rc-save-btn" style="margin-left:auto;">
        <i class="fas fa-floppy-disk"></i> Save All Permissions
    </button>
</div>
</form>

@endsection

@section('scripts')
<script>
function toggleModule(header) {
    header.classList.toggle('open');
    const featDiv = document.getElementById('features-' + header.parentElement.querySelector('[id^="features-"]').id.replace('features-', ''));
    // Better: find via sibling
    const features = header.nextElementSibling;
    if (features) features.classList.toggle('open');
}

// simpler toggle using data
document.querySelectorAll('.rc-module-header').forEach(hdr => {
    hdr.addEventListener('click', function(e) {
        if (e.target.closest('.rc-toggle-all')) return;
        this.classList.toggle('open');
        const features = this.nextElementSibling;
        if (features) features.classList.toggle('open');
    });
});

function toggleAllModule(moduleKey, btn) {
    const checkboxes = document.querySelectorAll('.mod-chk-' + moduleKey);
    const allOn = [...checkboxes].every(c => c.checked);
    checkboxes.forEach(c => c.checked = !allOn);
    btn.textContent = allOn ? 'Enable All' : 'Disable All';
    updateStats();
    updateModuleCount(moduleKey);
}

function updateModuleCount(moduleKey) {
    const checkboxes = document.querySelectorAll('.mod-chk-' + moduleKey);
    const on = [...checkboxes].filter(c => c.checked).length;
    const el = document.getElementById('count-' + moduleKey);
    if (el) el.textContent = on + ' / ' + checkboxes.length;
}

function updateStats() {
    const viewOn = document.querySelectorAll('.view-chk:checked').length;
    const editOn = document.querySelectorAll('.edit-chk:checked').length;
    document.getElementById('viewEnabled').textContent = viewOn;
    document.getElementById('editEnabled').textContent = editOn;
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    updateStats();
    @foreach($modules as $moduleKey => $module)
    updateModuleCount('{{ $moduleKey }}');
    @endforeach

    // Auto-open first module
    const first = document.querySelector('.rc-module-header');
    if (first) {
        first.classList.add('open');
        if (first.nextElementSibling) first.nextElementSibling.classList.add('open');
    }
});
</script>
@endsection
