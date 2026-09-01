@extends('superadmin.layouts.master')

@section('title', 'Selective Data Restore — ' . $school->name)

@section('styles')
<style>
    .restore-hub-container {
        max-width: 1320px;
        margin: 0 auto;
        padding-bottom: 90px;
    }

    /* Hero School Header */
    .school-scope-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .school-scope-hero::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .school-hero-title {
        font-size: 1.75rem;
        font-weight: 900;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
        color: #ffffff;
    }
    .scope-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #e0e7ff;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .tenant-lock-alert {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: 12px;
        padding: 12px 18px;
        color: #a7f3d0;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 18px;
    }

    /* Cards */
    .restore-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.2s ease;
    }
    .restore-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .restore-card-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Snapshot Selector */
    .snapshot-item-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .snapshot-item-card:hover {
        border-color: #93c5fd;
        background: #f0fdf4;
    }
    .snapshot-item-card.active {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.12);
    }

    /* Filter & Controls */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .category-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .cat-pill-btn {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .cat-pill-btn:hover, .cat-pill-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* Module Grid */
    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 16px;
    }
    .module-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .module-card:hover {
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }
    .module-card.selected {
        border-color: #2563eb;
        background: #f8faff;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.1);
    }
    .module-card-top {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 12px;
    }
    .module-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .module-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .module-category-tag {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .module-desc {
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.45;
        margin-bottom: 14px;
        flex-grow: 1;
    }
    .module-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
        font-size: 12px;
    }
    .module-row-badge {
        background: #ecfdf5;
        color: #065f46;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11.5px;
        border: 1px solid #a7f3d0;
    }
    .module-tables-count {
        color: #94a3b8;
        font-weight: 600;
    }

    /* Custom Checkbox Toggle */
    .module-checkbox-custom {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        border: 2px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 12px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .module-card.selected .module-checkbox-custom {
        background: #2563eb;
        border-color: #2563eb;
    }

    /* Sticky Bottom Action Bar */
    .restore-sticky-bar {
        position: fixed;
        bottom: 0;
        left: 270px;
        right: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border-top: 1px solid #e2e8f0;
        padding: 16px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 1040;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }
    @media (max-width: 991px) {
        .restore-sticky-bar {
            left: 0;
        }
    }

    .btn-restore-cta {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 800;
        padding: 12px 28px;
        border-radius: 12px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-restore-cta:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        color: #ffffff;
    }
    .btn-restore-cta:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Dark mode overrides */
    body.dark-mode .school-scope-hero {
        background: linear-gradient(135deg, #090d1f 0%, #030712 100%);
        border-color: #1e293b;
    }
    body.dark-mode .restore-card {
        background: #0f172a;
        border-color: #1e293b;
    }
    body.dark-mode .restore-card-title {
        color: #f8fafc;
    }
    body.dark-mode .snapshot-item-card {
        background: #1e293b;
        border-color: #334155;
    }
    body.dark-mode .snapshot-item-card.active {
        background: #172554;
        border-color: #3b82f6;
    }
    body.dark-mode .module-card {
        background: #0f172a;
        border-color: #1e293b;
    }
    body.dark-mode .module-card.selected {
        background: #172554;
        border-color: #3b82f6;
    }
    body.dark-mode .module-title {
        color: #f8fafc;
    }
    body.dark-mode .module-desc {
        color: #94a3b8;
    }
    body.dark-mode .cat-pill-btn {
        background: #1e293b;
        border-color: #334155;
        color: #cbd5e1;
    }
    body.dark-mode .restore-sticky-bar {
        background: rgba(15, 23, 42, 0.95);
        border-color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="restore-hub-container">

    {{-- Top Breadcrumb Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('superadmin.schools.index') }}" class="btn-sa-cancel" style="padding: 8px 18px; font-size: 13px; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to All Schools
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background:#eff6ff; color:#1d4ed8; font-size:12px; padding:6px 12px; border-radius:8px; font-weight:700; border:1px solid #bfdbfe;">
                <i class="fas fa-lock text-primary mr-1"></i> Tenant ID: #{{ $school->id }}
            </span>
            <span class="badge" style="background:#f1f5f9; color:#475569; font-size:12px; padding:6px 12px; border-radius:8px; font-weight:700; border:1px solid #e2e8f0;">
                Code: {{ $school->code }}
            </span>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 14px; font-weight: 600;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 14px; font-weight: 600;">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Hero School Context --}}
    <div class="school-scope-hero">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="scope-badge-pill">
                        <i class="fas fa-school text-warning"></i> Target School Profile
                    </span>
                    <span class="scope-badge-pill">
                        <i class="fas fa-code text-info"></i> {{ $school->code }}
                    </span>
                    <span class="scope-badge-pill">
                        {{ $school->state ?? 'UP' }} • {{ $school->board ?? 'CBSE' }}
                    </span>
                </div>
                <h1 class="school-hero-title">{{ $school->name }}</h1>
                <div class="d-flex flex-wrap align-items-center gap-4 text-sm" style="color: #cbd5e1; font-weight: 600;">
                    <span><i class="fas fa-user-tie text-muted mr-1"></i> Dir: {{ $school->director_name ?? $school->principal_name ?? 'School Administration' }}</span>
                    <span><i class="fas fa-envelope text-muted mr-1"></i> {{ $school->email ?? 'N/A' }}</span>
                    <span><i class="fas fa-user-graduate text-success mr-1"></i> Active Students: <strong>{{ $school->students()->where('is_active', 1)->count() }}</strong></span>
                    <span><i class="fas fa-chalkboard-teacher text-primary mr-1"></i> Staff: <strong>{{ $school->staff()->count() }}</strong></span>
                </div>
            </div>

            <div class="text-right">
                <button type="button" class="btn btn-warning fw-bold px-3 py-2" id="btnGenerateSnapshot" style="border-radius: 10px; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-camera"></i> Generate Fresh Snapshot
                </button>
            </div>
        </div>

        <div class="tenant-lock-alert">
            <i class="fas fa-shield-halved fa-lg"></i>
            <div>
                <strong>STRICT TENANT ISOLATION GUARANTEED:</strong> All data restoration operations on this page are strictly locked to <u>{{ $school->name }} (ID: {{ $school->id }})</u>. No records belonging to other schools will ever be displayed, modified, or restored.
            </div>
        </div>
    </div>

    {{-- Main Grid: Snapshot Selector & Modules --}}
    <form id="restoreForm" method="POST" action="{{ route('superadmin.schools.restore-data.execute', $school->id) }}">
        @csrf

        {{-- Section 1: Snapshot Archive Selection --}}
        <div class="restore-card">
            <div class="restore-card-header">
                <h3 class="restore-card-title">
                    <i class="fas fa-box-archive text-primary"></i> 1. Select Snapshot Archive
                </h3>
                <span class="badge badge-info" style="font-size: 12px; padding: 6px 12px; border-radius: 8px;">
                    {{ count($snapshots) }} Backup(s) Available for this School
                </span>
            </div>

            @if(empty($snapshots))
                <div class="text-center py-5" style="background: #f8fafc; border-radius: 14px; border: 2px dashed #cbd5e1;">
                    <div class="mb-3">
                        <i class="fas fa-box-open fa-3x text-muted"></i>
                    </div>
                    <h4 style="font-weight: 800; color: #334155;">No Snapshot Found for this School</h4>
                    <p class="text-muted" style="max-width: 480px; margin: 0 auto 20px; font-size: 13.5px;">
                        No backup archives were found in <code>storage/app/snapshots/</code> for School #{{ $school->id }}. You can generate a fresh snapshot immediately using the button below.
                    </p>
                    <button type="button" class="btn btn-primary fw-bold px-4 py-2" id="btnGenerateSnapshotEmpty" style="border-radius: 10px;">
                        <i class="fas fa-camera mr-1"></i> Generate First Snapshot for {{ $school->name }}
                    </button>
                </div>
            @else
                <input type="hidden" name="snapshot_file" id="selectedSnapshotInput" value="{{ $activeSnapshot['filename'] ?? '' }}">

                <div class="row">
                    @foreach($snapshots as $snap)
                        <div class="col-md-6 col-lg-4">
                            <div class="snapshot-item-card {{ ($activeSnapshot && $activeSnapshot['filename'] === $snap['filename']) ? 'active' : '' }}" 
                                 data-filename="{{ $snap['filename'] }}" 
                                 onclick="selectSnapshot('{{ $snap['filename'] }}', this)">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fas fa-file-zipper {{ $snap['is_latest'] ? 'text-success' : 'text-primary' }}"></i>
                                        <strong style="font-size: 13.5px; color: #0f172a;">{{ $snap['filename'] }}</strong>
                                    </div>
                                    <div class="text-xs text-muted font-weight-bold mb-2">
                                        <i class="far fa-calendar-alt mr-1"></i> {{ $snap['created_at'] }} • {{ $snap['size_formatted'] }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        @if($snap['is_latest'])
                                            <span class="badge badge-success" style="font-size: 10.5px; border-radius: 6px;">Latest Backup</span>
                                        @endif
                                        <span class="badge badge-light" style="font-size: 10.5px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                            <i class="fas fa-check-circle text-success mr-1"></i> SHA-256 Verified
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-check-circle fa-lg text-primary {{ ($activeSnapshot && $activeSnapshot['filename'] === $snap['filename']) ? '' : 'd-none' }} check-icon"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Section 2: Selective Module Catalog --}}
        <div class="restore-card">
            <div class="restore-card-header">
                <div>
                    <h3 class="restore-card-title">
                        <i class="fas fa-layer-group text-primary"></i> 2. Select Data Modules to Restore
                    </h3>
                    <p class="text-muted text-xs mb-0 mt-1">
                        Choose the specific modules you wish to recover. Only the selected modules will be restored. All unselected modules and other schools will remain 100% untouched.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="selectAllModules()" style="border-radius: 8px;">
                        <i class="fas fa-check-double mr-1"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="clearAllModules()" style="border-radius: 8px;">
                        <i class="fas fa-times mr-1"></i> Clear All
                    </button>
                </div>
            </div>

            {{-- Filter Bar & Search --}}
            <div class="filter-bar">
                <div class="category-pills" id="categoryFilterPills">
                    <button type="button" class="cat-pill-btn active" data-cat="all">All Modules ({{ count($modules) }})</button>
                    <button type="button" class="cat-pill-btn" data-cat="Academics & Students">Academics & Students</button>
                    <button type="button" class="cat-pill-btn" data-cat="Staff & HR">Staff & HR</button>
                    <button type="button" class="cat-pill-btn" data-cat="Finance & Accounts">Finance & Accounts</button>
                    <button type="button" class="cat-pill-btn" data-cat="Operations">Operations</button>
                    <button type="button" class="cat-pill-btn" data-cat="System">System</button>
                </div>

                <div style="min-width: 240px;">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="moduleSearchInput" class="form-control border-left-0" placeholder="Search modules..." onkeyup="filterModules()">
                    </div>
                </div>
            </div>

            {{-- Module Cards Grid --}}
            <div class="module-grid" id="moduleCardsContainer">
                @foreach($modules as $modKey => $mod)
                    @php
                        $rowCount = (int)($mod['snapshot_rows'] ?? 0);
                        $bgLight = $mod['color'] . '15';
                    @endphp
                    <div class="module-card" 
                         data-module-key="{{ $modKey }}" 
                         data-category="{{ $mod['category'] }}" 
                         data-rows="{{ $rowCount }}"
                         onclick="toggleModuleSelection('{{ $modKey }}', this)">
                        
                        {{-- Hidden Checkbox for Form Submission --}}
                        <input type="checkbox" 
                               name="restore_modules[]" 
                               value="{{ $modKey }}" 
                               id="chk_{{ $modKey }}" 
                               class="d-none module-checkbox-input"
                               onchange="updateSelectionCount()">

                        <div>
                            <div class="module-card-top">
                                <div class="module-icon-box" style="background: {{ $bgLight }}; color: {{ $mod['color'] }};">
                                    <i class="{{ $mod['icon'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="module-title">{{ $mod['name'] }}</div>
                                    <div class="module-category-tag">{{ $mod['category'] }}</div>
                                </div>
                                <div class="module-checkbox-custom">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>

                            <p class="module-desc">{{ $mod['description'] }}</p>
                        </div>

                        <div class="module-footer">
                            <span class="module-row-badge">
                                <i class="fas fa-database mr-1"></i> <span class="row-count-val">{{ number_format($rowCount) }}</span> in snapshot
                            </span>
                            <span class="module-tables-count">
                                {{ count($mod['tables']) }} table(s)
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Hidden Confirmation Token --}}
        <input type="checkbox" name="confirm_scope" id="confirmScopeInput" value="1" class="d-none">

        {{-- Sticky Action Bar --}}
        <div class="restore-sticky-bar">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 42px; height: 42px; background: #eff6ff; color: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 15px; color: #0f172a;">
                        Selected: <span id="barSelectedCount" class="text-primary">0</span> of {{ count($modules) }} modules
                    </div>
                    <div class="text-xs text-muted">
                        <span id="barSelectedRows">0</span> records queued for restoration in <u>{{ $school->name }}</u>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary fw-bold px-3 py-2" onclick="clearAllModules()" style="border-radius: 10px; font-size: 13.5px;">
                    Clear Selection
                </button>
                <button type="button" class="btn-restore-cta" id="btnTriggerPreview" onclick="openPreviewModal()" disabled>
                    <i class="fas fa-magic"></i> Preview & Restore Selected (<span id="btnModuleCount">0</span>)
                </button>
            </div>
        </div>
    </form>

    {{-- Section 3: Recent Disaster Recovery Logs for this School --}}
    @if($restoreLogs->isNotEmpty())
        <div class="restore-card mt-4">
            <div class="restore-card-header">
                <h3 class="restore-card-title">
                    <i class="fas fa-history text-secondary"></i> Recovery & Restoration Audit History ({{ $school->name }})
                </h3>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" style="font-size: 13px;">
                    <thead>
                        <tr class="text-muted">
                            <th>Timestamp</th>
                            <th>Admin Operator</th>
                            <th>Snapshot Archive</th>
                            <th>Modules Restored</th>
                            <th>Rows Restored</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($restoreLogs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <i class="fas fa-user-shield text-primary mr-1"></i> {{ $log->user->name ?? 'Super Admin' }}
                                </td>
                                <td><code>{{ $log->snapshot_file }}</code></td>
                                <td>
                                    @php $mods = (array)($log->modules_selected ?? []); @endphp
                                    <span class="badge badge-info">{{ count($mods) }} module(s)</span>
                                </td>
                                <td>
                                    <strong class="text-success">+{{ number_format($log->total_rows_inserted) }}</strong>
                                </td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Success</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> {{ ucfirst($log->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- Confirmation & Safety Preview Modal --}}
<div class="modal fade" id="previewRestoreModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            
            {{-- Modal Header --}}
            <div class="modal-header" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); color: #ffffff; padding: 22px 28px; border-bottom: none;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; background: rgba(99, 102, 241, 0.2); border: 1px solid rgba(99, 102, 241, 0.4); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #818cf8;">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold" style="font-size: 1.25rem;">Confirm Selective Recovery</h5>
                        <p class="text-xs mb-0" style="color: #cbd5e1;">Review the target school and selected module scope before execution</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4">
                {{-- Target School Scope Card --}}
                <div class="p-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <span class="text-xs text-muted font-weight-bold text-uppercase">Target School</span>
                            <div class="font-weight-bold text-dark" style="font-size: 15px;">{{ $school->name }}</div>
                            <div class="text-xs text-muted">ID: #{{ $school->id }} • Code: {{ $school->code }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-xs text-muted font-weight-bold text-uppercase">Snapshot Archive</span>
                            <div class="font-weight-bold text-primary text-truncate" id="modalSnapshotName">...</div>
                        </div>
                    </div>
                </div>

                {{-- Selected Modules Summary Table --}}
                <h6 class="font-weight-bold text-dark mb-2" style="font-size: 13.5px;">
                    <i class="fas fa-list-check text-primary mr-1"></i> Modules Queued for Restoration:
                </h6>
                <div class="table-responsive mb-3" style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <table class="table table-sm table-striped mb-0" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th>Module</th>
                                <th>Category</th>
                                <th class="text-right">Snapshot Records</th>
                            </tr>
                        </thead>
                        <tbody id="modalModuleTableBody">
                            {{-- Populated dynamically by JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- Safety Guarantees Checklist --}}
                <div class="p-3 mb-3" style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; font-size: 12.5px; color: #065f46;">
                    <div class="font-weight-bold mb-1"><i class="fas fa-check-circle text-success mr-1"></i> Safety & Isolation Guarantee:</div>
                    <ul class="mb-0 pl-3">
                        <li>Restoration operates <strong>exclusively</strong> on School ID #{{ $school->id }} ({{ $school->name }}).</li>
                        <li><strong>Zero cross-tenant impact</strong>: All other schools will remain untouched.</li>
                        <li><strong>Unselected modules</strong> for this school will remain untouched.</li>
                        <li>Automatic pre-restore database safety backup will be taken.</li>
                        <li>Operation runs inside an atomic database transaction with automatic rollback if an error occurs.</li>
                    </ul>
                </div>

                {{-- Mandatory Confirmation Checkbox --}}
                <div class="custom-control custom-checkbox p-2" style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;">
                    <input type="checkbox" class="custom-control-input" id="chkModalConfirm" onchange="toggleConfirmSubmitBtn()">
                    <label class="custom-control-label font-weight-bold text-dark" for="chkModalConfirm" style="font-size: 13px; cursor: pointer;">
                        I authorize and confirm the selective restoration of the selected modules for <u>{{ $school->name }}</u> only.
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer bg-light p-3" style="border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-dismiss="modal" style="border-radius: 10px;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary px-4 fw-bold" id="btnConfirmExecute" onclick="submitRestoreForm()" disabled style="border-radius: 10px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                    <i class="fas fa-bolt mr-1"></i> Confirm & Execute Restore
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const moduleCatalog = @json($modules);
    let selectedModules = new Set();

    function toggleModuleSelection(key, element) {
        const checkbox = document.getElementById('chk_' + key);
        if (!checkbox) return;

        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            selectedModules.add(key);
            element.classList.add('selected');
        } else {
            selectedModules.delete(key);
            element.classList.remove('selected');
        }
        updateSelectionCount();
    }

    function selectAllModules() {
        document.querySelectorAll('.module-card').forEach(card => {
            const key = card.getAttribute('data-module-key');
            const checkbox = document.getElementById('chk_' + key);
            if (checkbox && card.style.display !== 'none') {
                checkbox.checked = true;
                selectedModules.add(key);
                card.classList.add('selected');
            }
        });
        updateSelectionCount();
    }

    function clearAllModules() {
        document.querySelectorAll('.module-card').forEach(card => {
            const key = card.getAttribute('data-module-key');
            const checkbox = document.getElementById('chk_' + key);
            if (checkbox) {
                checkbox.checked = false;
                selectedModules.delete(key);
                card.classList.remove('selected');
            }
        });
        updateSelectionCount();
    }

    function updateSelectionCount() {
        const count = selectedModules.size;
        let totalRows = 0;

        selectedModules.forEach(key => {
            if (moduleCatalog[key]) {
                totalRows += parseInt(moduleCatalog[key].snapshot_rows || 0);
            }
        });

        document.getElementById('barSelectedCount').innerText = count;
        document.getElementById('barSelectedRows').innerText = totalRows.toLocaleString();
        document.getElementById('btnModuleCount').innerText = count;

        const btnTrigger = document.getElementById('btnTriggerPreview');
        if (count > 0) {
            btnTrigger.removeAttribute('disabled');
        } else {
            btnTrigger.setAttribute('disabled', 'disabled');
        }
    }

    function selectSnapshot(filename, element) {
        document.querySelectorAll('.snapshot-item-card').forEach(c => {
            c.classList.remove('active');
            const icon = c.querySelector('.check-icon');
            if (icon) icon.classList.add('d-none');
        });

        element.classList.add('active');
        const icon = element.querySelector('.check-icon');
        if (icon) icon.classList.remove('d-none');

        document.getElementById('selectedSnapshotInput').value = filename;

        // AJAX inspect module row counts for this newly selected snapshot
        inspectSnapshotAjax(filename);
    }

    function inspectSnapshotAjax(filename) {
        fetch("{{ route('superadmin.schools.restore-data.inspect', $school->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ snapshot_file: filename })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.inspection && data.inspection.modules) {
                const inspMods = data.inspection.modules;
                Object.keys(inspMods).forEach(key => {
                    if (moduleCatalog[key]) {
                        moduleCatalog[key].snapshot_rows = inspMods[key].snapshot_rows;
                    }
                    const card = document.querySelector(`.module-card[data-module-key="${key}"]`);
                    if (card) {
                        const rowValSpan = card.querySelector('.row-count-val');
                        if (rowValSpan) {
                            rowValSpan.innerText = parseInt(inspMods[key].snapshot_rows || 0).toLocaleString();
                        }
                    }
                });
                updateSelectionCount();
            }
        })
        .catch(err => console.error("Inspection error:", err));
    }

    // Category Filtering
    document.querySelectorAll('.cat-pill-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.cat-pill-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const cat = this.getAttribute('data-cat');

            document.querySelectorAll('.module-card').forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (cat === 'all' || cardCat === cat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Search Filtering
    function filterModules() {
        const query = document.getElementById('moduleSearchInput').value.toLowerCase().trim();
        document.querySelectorAll('.module-card').forEach(card => {
            const text = card.innerText.toLowerCase();
            if (text.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Preview Modal Logic
    function openPreviewModal() {
        if (selectedModules.size === 0) return;

        const snapshotFile = document.getElementById('selectedSnapshotInput').value || 'Latest Available Snapshot';
        document.getElementById('modalSnapshotName').innerText = snapshotFile;

        const tbody = document.getElementById('modalModuleTableBody');
        tbody.innerHTML = '';

        selectedModules.forEach(key => {
            const mod = moduleCatalog[key];
            if (mod) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="font-weight-bold text-dark">
                        <i class="${mod.icon} mr-1" style="color: ${mod.color};"></i> ${mod.name}
                    </td>
                    <td class="text-muted">${mod.category}</td>
                    <td class="text-right font-weight-bold text-success">${parseInt(mod.snapshot_rows || 0).toLocaleString()}</td>
                `;
                tbody.appendChild(tr);
            }
        });

        document.getElementById('chkModalConfirm').checked = false;
        document.getElementById('btnConfirmExecute').setAttribute('disabled', 'disabled');

        $('#previewRestoreModal').modal('show');
    }

    function toggleConfirmSubmitBtn() {
        const chk = document.getElementById('chkModalConfirm');
        const btn = document.getElementById('btnConfirmExecute');
        if (chk.checked) {
            btn.removeAttribute('disabled');
        } else {
            btn.setAttribute('disabled', 'disabled');
        }
    }

    function submitRestoreForm() {
        const confirmScope = document.getElementById('confirmScopeInput');
        confirmScope.checked = true;

        const btn = document.getElementById('btnConfirmExecute');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Restoring Data...';
        btn.setAttribute('disabled', 'disabled');

        document.getElementById('restoreForm').submit();
    }

    // Snapshot On-Demand Generation
    function handleGenerateSnapshot(btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Generating...';
        btn.setAttribute('disabled', 'disabled');

        fetch("{{ route('superadmin.schools.restore-data.snapshot', $school->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert("Error: " + data.message);
                btn.innerHTML = '<i class="fas fa-camera mr-1"></i> Generate Fresh Snapshot';
                btn.removeAttribute('disabled');
            }
        })
        .catch(err => {
            alert("Snapshot creation failed. Please check server logs.");
            btn.innerHTML = '<i class="fas fa-camera mr-1"></i> Generate Fresh Snapshot';
            btn.removeAttribute('disabled');
        });
    }

    const btnGen = document.getElementById('btnGenerateSnapshot');
    if (btnGen) {
        btnGen.addEventListener('click', function() {
            handleGenerateSnapshot(this);
        });
    }

    const btnGenEmpty = document.getElementById('btnGenerateSnapshotEmpty');
    if (btnGenEmpty) {
        btnGenEmpty.addEventListener('click', function() {
            handleGenerateSnapshot(this);
        });
    }
</script>
@endsection
