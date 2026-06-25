@extends('layouts.app')

@section('title', 'Basic Institute Info')
@section('page-title', 'Basic Institute Info')

@section('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   BASIC INSTITUTE INFO — Premium Blue & White Theme
   Designed for Visual Excellence, Micro-interactions, & Modals
   ═══════════════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.inst-page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f4f7fc;
    color: #1e293b;
    padding-bottom: 48px;
}

/* ── HEADER ACTIONS ────────────────────────────────────────── */
.inst-header-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 16px;
}
.inst-btn-social {
    background: #fff;
    color: #2563eb;
    border: 1.5px solid #2563eb;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.05);
}
.inst-btn-social:hover {
    background: #2563eb;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

/* ── TOP SECTION: DETAILS BLOCK ────────────────────────────── */
.inst-details-panel {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(30, 58, 138, 0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 24px;
    position: relative;
    transition: border-color 0.2s ease;
}
.inst-details-panel:hover {
    border-color: #cbd5e1;
}
.inst-details-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 16px;
    border-bottom: 1.5px solid #f1f5f9;
    margin-bottom: 20px;
}
.inst-name-title {
    font-size: 20px;
    font-weight: 800;
    color: #1e3a8a;
    margin: 0;
    letter-spacing: -0.5px;
}
.inst-btn-edit {
    background: #fff;
    color: #2563eb;
    border: 1.5px solid #2563eb;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}
.inst-btn-edit:hover {
    background: #f0f5ff;
    transform: translateY(-1px);
}

/* Grid Layout for details & assets */
.inst-details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 20px;
    align-items: stretch;
}
.inst-details-col {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.inst-detail-item {
    display: flex;
    font-size: 13.5px;
    line-height: 1.5;
}
.inst-detail-lbl {
    font-weight: 700;
    color: #64748b;
    width: 200px;
    flex-shrink: 0;
}
.inst-detail-colon {
    color: #64748b;
    margin-right: 12px;
}
.inst-detail-val {
    color: #1e293b;
    font-weight: 600;
}

/* Asset columns (logo, stamp, signature) */
.inst-asset-col {
    border-left: 1px solid #f1f5f9;
    padding-left: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.inst-asset-box {
    width: 100%;
    height: 90px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 8px;
    padding: 6px;
    transition: all 0.2s ease;
}
.inst-asset-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.inst-asset-lbl {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.inst-asset-missing {
    font-size: 12px;
    color: #ef4444;
    font-weight: 600;
}

/* ── TWO COLUMN LAYOUT ─────────────────────────────────────── */
.inst-two-col {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 24px;
}

/* Panel Design */
.inst-panel {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(30, 58, 138, 0.05);
    border: 1px solid #e2e8f0;
}
.inst-panel-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1.5px solid #f1f5f9;
    margin-bottom: 16px;
}
.inst-panel-title {
    font-size: 16px;
    font-weight: 800;
    color: #1e3a8a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.inst-panel-title i {
    color: #2563eb;
}
.inst-panel-btn-edit {
    background: none;
    border: none;
    color: #2563eb;
    cursor: pointer;
    font-size: 16px;
    transition: transform 0.2s ease;
}
.inst-panel-btn-edit:hover {
    transform: scale(1.15);
}

/* Time Table */
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
.inst-table th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
.inst-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    font-weight: 600;
    color: #475569;
}
.inst-table tr:hover td {
    background: #f8fafc;
    color: #1e293b;
}

/* Card Stack */
.inst-card-stack {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.inst-action-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(30, 58, 138, 0.05);
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.25s ease;
}
.inst-action-card:hover {
    transform: translateY(-2px);
    border-color: #cbd5e1;
}
.inst-card-icon {
    font-size: 32px;
    color: #3b82f6;
    margin-bottom: 12px;
    background: #eff6ff;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.inst-action-card:hover .inst-card-icon {
    transform: rotate(5deg) scale(1.05);
}
.inst-card-title {
    font-size: 14.5px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 6px;
}
.inst-card-sub {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 14px;
}
.inst-card-btn {
    background: #fff;
    color: #2563eb;
    border: 1.5px solid #2563eb;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}
.inst-card-btn:hover {
    background: #2563eb;
    color: #fff;
}

/* Badges for lists in cards */
.inst-badge-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
    margin-bottom: 12px;
    max-width: 90%;
}
.inst-badge {
    padding: 3px 8px;
    border-radius: 30px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
}
.inst-badge.b-house { background: #e0f2fe; color: #0369a1; }
.inst-badge.b-group { background: #ecfdf5; color: #047857; }

/* ── MODAL STYLING ────────────────────────────────────────── */
.inst-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
}
.inst-modal.active {
    display: flex;
    animation: fadeIn 0.25s ease;
}
.inst-modal-content {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 580px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    border: 1px solid #cbd5e1;
    overflow: hidden;
    animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
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
    color: rgba(255, 255, 255, 0.7);
    font-size: 18px;
    cursor: pointer;
    transition: color 0.2s ease;
}
.inst-modal-close:hover {
    color: #fff;
}
.inst-modal-body {
    padding: 24px;
    max-height: 75vh;
    overflow-y: auto;
}

/* Form layouts */
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
}
.inst-form-control {
    padding: 10px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13.5px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s ease;
}
.inst-form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.inst-form-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 24px;
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
}
.inst-modal-list {
    margin-bottom: 16px;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    max-height: 150px;
    overflow-y: auto;
}
.inst-modal-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
}
.inst-modal-list-item:last-child {
    border-bottom: none;
}
.inst-btn-delete {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 13px;
}
.inst-btn-delete:hover {
    color: #dc2626;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
@endsection

@section('content')
<div class="inst-page">

    {{-- ── TOP HEADER ACTIONS ── --}}
    <div class="inst-header-actions">
        <button class="inst-btn-social" onclick="openInstModal('modal-social')">
            <i class="fas fa-plus"></i> ADD SOCIAL MEDIA
        </button>
    </div>

    {{-- ── MAIN DETAILS PANEL ── --}}
    <div class="inst-details-panel">
        <div class="inst-details-hdr">
            <h2 class="inst-name-title">{{ $school->name }}</h2>
            <button class="inst-btn-edit" onclick="openInstModal('modal-details')">
                <i class="fas fa-edit"></i> EDIT DETAILS
            </button>
        </div>

        <div class="inst-details-grid">
            {{-- Details List Column --}}
            <div class="inst-details-col">
                <div class="inst-detail-item">
                    <span class="inst-detail-lbl">Institute Affiliation Number</span>
                    <span class="inst-detail-colon">:</span>
                    <span class="inst-detail-val">{{ $udise['affiliation_number'] ?? '-' }}</span>
                </div>
                <div class="inst-detail-item">
                    <span class="inst-detail-lbl">UDISE Number</span>
                    <span class="inst-detail-colon">:</span>
                    <span class="inst-detail-val">{{ $udise['udise_number'] ?? '-' }}</span>
                </div>
                <div class="inst-detail-item">
                    <span class="inst-detail-lbl">Institute Code</span>
                    <span class="inst-detail-colon">:</span>
                    <span class="inst-detail-val">{{ $school->code }}</span>
                </div>
                <div class="inst-detail-item">
                    <span class="inst-detail-lbl">Board Name</span>
                    <span class="inst-detail-colon">:</span>
                    <span class="inst-detail-val">{{ $udise['board_name'] ?? '-' }}</span>
                </div>
                <div class="inst-detail-item">
                    <span class="inst-detail-lbl">Academic Session</span>
                    <span class="inst-detail-colon">:</span>
                    <span class="inst-detail-val">{{ $currentSession->name ?? '-' }}</span>
                </div>
                <div class="inst-detail-item">
                    <span class="inst-detail-lbl">Session Start / End</span>
                    <span class="inst-detail-colon">:</span>
                    <span class="inst-detail-val">
                        @if(isset($currentSession))
                            {{ $currentSession->start_date ? $currentSession->start_date->format('d/m/Y') : '-' }} to {{ $currentSession->end_date ? $currentSession->end_date->format('d/m/Y') : '-' }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>

            {{-- Logo Column --}}
            <div class="inst-asset-col">
                <div class="inst-asset-box">
                    @if(!empty($school->logo))
                        <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="Logo" style="max-width:100%;max-height:100%;object-fit:contain;">
                    @else
                        <i class="fas fa-image" style="font-size:32px; color:#cbd5e1;"></i>
                    @endif
                </div>
                <span class="inst-asset-lbl">Logo</span>
            </div>

            {{-- Stamp Column --}}
            <div class="inst-asset-col">
                <div class="inst-asset-box">
                    @if(!empty($udise['stamp']))
                        <img src="{{ Storage::disk('public')->url($udise['stamp']) }}" alt="Stamp">
                    @else
                        <span class="inst-asset-missing">No stamp added yet</span>
                    @endif
                </div>
                <span class="inst-asset-lbl">Stamp</span>
            </div>

            {{-- Signature Column --}}
            <div class="inst-asset-col">
                <div class="inst-asset-box">
                    @if(!empty($udise['signature']))
                        <img src="{{ Storage::disk('public')->url($udise['signature']) }}" alt="Signature">
                    @else
                        <span class="inst-asset-missing">No signature added yet</span>
                    @endif
                </div>
                <span class="inst-asset-lbl">Signature</span>
            </div>
        </div>
    </div>

    {{-- ── TWO COLUMN SECTION ── --}}
    <div class="inst-two-col">
        {{-- Left: Institute Days and Time --}}
        <div class="inst-panel">
            <div class="inst-panel-hdr">
                <h3 class="inst-panel-title">
                    <i class="fas fa-clock"></i>
                    <span>Institute Days and Time</span>
                </h3>
                <button class="inst-panel-btn-edit" onclick="openInstModal('modal-hours')">
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </div>

            <table class="inst-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Day</th>
                        <th style="width: 30%;">Start Time</th>
                        <th style="width: 30%;">End Time</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $timings = $udise['days_and_time'] ?? [];
                    @endphp
                    @foreach($days as $day)
                        <tr>
                            <td>{{ $day }}</td>
                            <td>{{ $timings[$day]['start_time'] ?? '-' }}</td>
                            <td>{{ $timings[$day]['end_time'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Right: Actions Cards Stack --}}
        <div class="inst-card-stack">
            {{-- Houses Card --}}
            <div class="inst-action-card">
                <div class="inst-card-icon"><i class="fas fa-home"></i></div>
                <div class="inst-card-title">Update Institute's Houses</div>
                <div class="inst-card-sub">Manage school house categorizations for activities</div>
                
                @if($houses->isNotEmpty())
                    <div class="inst-badge-list">
                        @foreach($houses as $h)
                            <span class="inst-badge b-house" style="border-left: 4px solid {{ $h->color_code }};">
                                {{ $h->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
                
                <button class="inst-card-btn" onclick="openInstModal('modal-houses')">
                    <i class="fas fa-edit"></i> ADD HOUSES
                </button>
            </div>

            {{-- Groups Card --}}
            <div class="inst-action-card">
                <div class="inst-card-icon"><i class="fas fa-users"></i></div>
                <div class="inst-card-title">Update Institute's Groups</div>
                <div class="inst-card-sub">Manage student groups & reporting categories</div>
                
                @if($groups->isNotEmpty())
                    <div class="inst-badge-list">
                        @foreach($groups as $g)
                            <span class="inst-badge b-group">
                                {{ $g->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <button class="inst-card-btn" onclick="openInstModal('modal-groups')">
                    <i class="fas fa-edit"></i> ADD GROUPS
                </button>
            </div>

            {{-- Contact Information Card --}}
            <div class="inst-action-card">
                <div class="inst-card-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="inst-card-title">Update Institute's Contact Information</div>
                <div class="inst-card-sub">
                    @if(!empty($school->phone) || !empty($school->email) || !empty($school->address))
                        <div style="font-size:12px; color:#475569; line-height:1.4; text-align:left; margin-bottom:8px;">
                            @if(!empty($school->phone)) <div><i class="fas fa-phone" style="width:14px;"></i> {{ $school->phone }}</div> @endif
                            @if(!empty($school->email)) <div><i class="fas fa-envelope" style="width:14px;"></i> {{ $school->email }}</div> @endif
                            @if(!empty($school->address)) <div style="display:flex; gap:4px;"><i class="fas fa-map-marker-alt" style="width:14px; margin-top:2px;"></i> <span style="flex-grow:1;">{{ $school->address }}</span></div> @endif
                        </div>
                    @else
                        Official address, contact numbers, & emails
                    @endif
                </div>
                <button class="inst-card-btn" onclick="openInstModal('modal-contact')">
                    <i class="fas fa-edit"></i> ADD CONTACT INFORMATION
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
       MODALS
       ═══════════════════════════════════════════════════════════════ --}}

    {{-- Modal 1: Edit Details --}}
    <div class="inst-modal" id="modal-details">
        <div class="inst-modal-content">
            <div class="inst-modal-hdr">
                <h3>Edit Institute Information</h3>
                <button class="inst-modal-close" onclick="closeInstModal('modal-details')">&times;</button>
            </div>
            <div class="inst-modal-body">
                <form method="POST" action="{{ route('school.settings.institute-info.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;margin-bottom:16px;">
                        <div style="font-size:12.5px;font-weight:700;color:#991b1b;margin-bottom:4px;"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>Please fix the following errors:</div>
                        <ul style="margin:0;padding-left:16px;font-size:12px;color:#b91c1c;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="inst-form-group">
                        <label class="inst-form-label">Institute Name *</label>
                        <input type="text" name="name" class="inst-form-control" value="{{ old('name', $school->name) }}" required>
                    </div>

                    <div class="inst-form-grid">
                        <div class="inst-form-group">
                            <label class="inst-form-label">Institute Code *</label>
                            <input type="text" name="code" class="inst-form-control" value="{{ old('code', $school->code) }}" required>
                        </div>
                        <div class="inst-form-group">
                            <label class="inst-form-label">Board Name</label>
                            <input type="text" name="board_name" class="inst-form-control" value="{{ old('board_name', $udise['board_name'] ?? '') }}" placeholder="e.g. CBSE">
                        </div>
                    </div>

                    <div class="inst-form-grid">
                        <div class="inst-form-group">
                            <label class="inst-form-label">Affiliation Number</label>
                            <input type="text" name="affiliation_number" class="inst-form-control" value="{{ old('affiliation_number', $udise['affiliation_number'] ?? '') }}">
                        </div>
                        <div class="inst-form-group">
                            <label class="inst-form-label">UDISE Number</label>
                            <input type="text" name="udise_number" class="inst-form-control" value="{{ old('udise_number', $udise['udise_number'] ?? '') }}" placeholder="11-digit code" maxlength="11">
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e2e8f0; margin: 16px 0; padding-top: 16px;">
                        <h4 style="font-size: 13.5px; font-weight: 800; color: #1e3a8a; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Academic Year Configuration</h4>
                        <div class="inst-form-group">
                            <label class="inst-form-label">Academic Session Name *</label>
                            <input type="text" name="academic_session_name" class="inst-form-control" value="{{ old('academic_session_name', $currentSession->name ?? '') }}" required placeholder="e.g. 2025-26">
                        </div>
                        <div class="inst-form-grid">
                            <div class="inst-form-group">
                                <label class="inst-form-label">Session Start Date *</label>
                                <input type="date" name="academic_session_start_date" class="inst-form-control" value="{{ old('academic_session_start_date', isset($currentSession) && $currentSession->start_date ? $currentSession->start_date->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="inst-form-group">
                                <label class="inst-form-label">Session End Date *</label>
                                <input type="date" name="academic_session_end_date" class="inst-form-control" value="{{ old('academic_session_end_date', isset($currentSession) && $currentSession->end_date ? $currentSession->end_date->format('Y-m-d') : '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="inst-form-group">
                        <label class="inst-form-label">Institute Logo</label>
                        <input type="file" name="logo" class="inst-form-control" accept="image/*">
                    </div>

                    <div class="inst-form-grid">
                        <div class="inst-form-group">
                            <label class="inst-form-label">Official Stamp</label>
                            <input type="file" name="stamp" class="inst-form-control" accept="image/*">
                        </div>
                        <div class="inst-form-group">
                            <label class="inst-form-label">Principal Signature</label>
                            <input type="file" name="signature" class="inst-form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn btn-outline" onclick="closeInstModal('modal-details')" style="padding: 10px 20px; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; background:#2563eb; border-color:#2563eb; color:#fff;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 2: Edit Hours --}}
    <div class="inst-modal" id="modal-hours">
        <div class="inst-modal-content" style="max-width: 650px;">
            <div class="inst-modal-hdr">
                <h3>Edit Institute Days & Timings</h3>
                <button class="inst-modal-close" onclick="closeInstModal('modal-hours')">&times;</button>
            </div>
            <div class="inst-modal-body">
                <form method="POST" action="{{ route('school.settings.institute-hours.update') }}">
                    @csrf
                    @method('PUT')

                    <div style="max-height: 50vh; overflow-y: auto; padding-right: 6px;">
                        @foreach($days as $day)
                            <div style="display:grid; grid-template-columns: 1.5fr 2fr 2fr; gap:16px; align-items:center; margin-bottom:12px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                                <strong style="font-size:13px; color:#1e293b;">{{ $day }}</strong>
                                <div class="inst-form-group" style="margin-bottom:0;">
                                    <input type="text" name="hours[{{ $day }}][start_time]" class="inst-form-control" value="{{ $timings[$day]['start_time'] ?? '' }}" placeholder="e.g. 08:00 AM" style="padding:6px 10px; font-size:13px;">
                                </div>
                                <div class="inst-form-group" style="margin-bottom:0;">
                                    <input type="text" name="hours[{{ $day }}][end_time]" class="inst-form-control" value="{{ $timings[$day]['end_time'] ?? '' }}" placeholder="e.g. 02:00 PM" style="padding:6px 10px; font-size:13px;">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn btn-outline" onclick="closeInstModal('modal-hours')" style="padding: 10px 20px; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; background:#2563eb; border-color:#2563eb; color:#fff;">Save Timings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 3: Houses --}}
    <div class="inst-modal" id="modal-houses">
        <div class="inst-modal-content">
            <div class="inst-modal-hdr">
                <h3>Manage Student Houses</h3>
                <button class="inst-modal-close" onclick="closeInstModal('modal-houses')">&times;</button>
            </div>
            <div class="inst-modal-body">
                {{-- Existing Houses List --}}
                <div class="inst-form-label" style="margin-bottom:8px;">Existing Houses</div>
                <div class="inst-modal-list">
                    @forelse($houses as $h)
                        <div class="inst-modal-list-item">
                            <span style="display:flex; align-items:center; gap:8px; font-weight:600;">
                                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:{{ $h->color_code }}; border:1px solid #cbd5e1;"></span>
                                {{ $h->name }}
                            </span>
                            <form method="POST" action="{{ route('school.settings.houses.destroy', $h->id) }}" onsubmit="return confirm('Are you sure you want to delete this house?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inst-btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    @empty
                        <div style="padding: 12px; text-align:center; color:#94a3b8; font-size:13px;">No houses added yet.</div>
                    @endforelse
                </div>

                {{-- Add House Form --}}
                <form method="POST" action="{{ route('school.settings.houses.store') }}" style="border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: 16px;">
                    @csrf
                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:12px;">
                        <div class="inst-form-group" style="margin-bottom:0;">
                            <label class="inst-form-label">House Name *</label>
                            <input type="text" name="name" class="inst-form-control" required placeholder="e.g. Red House" style="padding: 8px 12px; font-size:13px;">
                        </div>
                        <div class="inst-form-group" style="margin-bottom:0;">
                            <label class="inst-form-label">Color Code</label>
                            <input type="color" name="color_code" class="inst-form-control" value="#2563eb" style="padding: 3px 6px; height: 38px; width: 100%;">
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end; margin-top:14px;">
                        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size:12px; background:#2563eb; border-color:#2563eb; color:#fff;">+ Add House</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 4: Groups (Categories) --}}
    <div class="inst-modal" id="modal-groups">
        <div class="inst-modal-content">
            <div class="inst-modal-hdr">
                <h3>Manage Student Groups / Categories</h3>
                <button class="inst-modal-close" onclick="closeInstModal('modal-groups')">&times;</button>
            </div>
            <div class="inst-modal-body">
                {{-- Existing Groups List --}}
                <div class="inst-form-label" style="margin-bottom:8px;">Existing Categories / Groups</div>
                <div class="inst-modal-list">
                    @forelse($groups as $g)
                        <div class="inst-modal-list-item">
                            <span style="font-weight:600;">{{ $g->name }} @if($g->description) <span style="font-weight:400; color:#64748b; font-size:11px;">({{ $g->description }})</span> @endif</span>
                            <form method="POST" action="{{ route('school.settings.groups.destroy', $g->id) }}" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inst-btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    @empty
                        <div style="padding: 12px; text-align:center; color:#94a3b8; font-size:13px;">No categories/groups added yet.</div>
                    @endforelse
                </div>

                {{-- Add Group Form --}}
                <form method="POST" action="{{ route('school.settings.groups.store') }}" style="border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: 16px;">
                    @csrf
                    <div class="inst-form-group">
                        <label class="inst-form-label">Category Name *</label>
                        <input type="text" name="name" class="inst-form-control" required placeholder="e.g. General, OBC, Staff Child" style="padding: 8px 12px; font-size:13px;">
                    </div>
                    <div class="inst-form-group">
                        <label class="inst-form-label">Description</label>
                        <input type="text" name="description" class="inst-form-control" placeholder="Optional details" style="padding: 8px 12px; font-size:13px;">
                    </div>
                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size:12px; background:#2563eb; border-color:#2563eb; color:#fff;">+ Add Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 5: Social Media --}}
    <div class="inst-modal" id="modal-social">
        <div class="inst-modal-content">
            <div class="inst-modal-hdr">
                <h3>Update Institute Social Media Links</h3>
                <button class="inst-modal-close" onclick="closeInstModal('modal-social')">&times;</button>
            </div>
            <div class="inst-modal-body">
                <form method="POST" action="{{ route('school.settings.social-media.update') }}">
                    @csrf
                    @php
                        $social = $udise['social_media'] ?? [];
                    @endphp
                    <div class="inst-form-group">
                        <label class="inst-form-label"><i class="fab fa-facebook" style="color:#1877f2; margin-right:4px;"></i> Facebook URL</label>
                        <input type="url" name="facebook" class="inst-form-control" value="{{ $social['facebook'] ?? '' }}" placeholder="https://facebook.com/yourschool">
                    </div>
                    <div class="inst-form-group">
                        <label class="inst-form-label"><i class="fab fa-twitter" style="color:#1da1f2; margin-right:4px;"></i> Twitter / X URL</label>
                        <input type="url" name="twitter" class="inst-form-control" value="{{ $social['twitter'] ?? '' }}" placeholder="https://twitter.com/yourschool">
                    </div>
                    <div class="inst-form-group">
                        <label class="inst-form-label"><i class="fab fa-instagram" style="color:#e1306c; margin-right:4px;"></i> Instagram URL</label>
                        <input type="url" name="instagram" class="inst-form-control" value="{{ $social['instagram'] ?? '' }}" placeholder="https://instagram.com/yourschool">
                    </div>
                    <div class="inst-form-group">
                        <label class="inst-form-label"><i class="fab fa-youtube" style="color:#ff0000; margin-right:4px;"></i> YouTube Channel URL</label>
                        <input type="url" name="youtube" class="inst-form-control" value="{{ $social['youtube'] ?? '' }}" placeholder="https://youtube.com/c/yourschool">
                    </div>
                    <div class="inst-form-group">
                        <label class="inst-form-label"><i class="fab fa-linkedin" style="color:#0a66c2; margin-right:4px;"></i> LinkedIn Page URL</label>
                        <input type="url" name="linkedin" class="inst-form-control" value="{{ $social['linkedin'] ?? '' }}" placeholder="https://linkedin.com/school/yourschool">
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn btn-outline" onclick="closeInstModal('modal-social')" style="padding: 10px 20px; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; background:#2563eb; border-color:#2563eb; color:#fff;">Save Links</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 6: Contact Information --}}
    <div class="inst-modal" id="modal-contact">
        <div class="inst-modal-content">
            <div class="inst-modal-hdr">
                <h3>Update Contact Details</h3>
                <button class="inst-modal-close" onclick="closeInstModal('modal-contact')">&times;</button>
            </div>
            <div class="inst-modal-body">
                <form method="POST" action="{{ route('school.settings.institute-info.update') }}">
                    @csrf
                    @method('PUT')
                    
                    {{-- Hidden fields to satisfy form validation on other attributes --}}
                    <input type="hidden" name="name" value="{{ $school->name }}">
                    <input type="hidden" name="code" value="{{ $school->code }}">
                    <input type="hidden" name="affiliation_number" value="{{ $udise['affiliation_number'] ?? '' }}">
                    <input type="hidden" name="udise_number" value="{{ $udise['udise_number'] ?? '' }}">
                    <input type="hidden" name="board_name" value="{{ $udise['board_name'] ?? '' }}">

                    <div class="inst-form-group">
                        <label class="inst-form-label">Contact Phone</label>
                        <input type="text" name="phone" class="inst-form-control" value="{{ old('phone', $school->phone) }}">
                    </div>

                    <div class="inst-form-group">
                        <label class="inst-form-label">Official Email</label>
                        <input type="email" name="email" class="inst-form-control" value="{{ old('email', $school->email) }}">
                    </div>

                    <div class="inst-form-group">
                        <label class="inst-form-label">Address</label>
                        <textarea name="address" class="inst-form-control" rows="4">{{ old('address', $school->address) }}</textarea>
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn btn-outline" onclick="closeInstModal('modal-contact')" style="padding: 10px 20px; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; background:#2563eb; border-color:#2563eb; color:#fff;">Save Contact Info</button>
                    </div>
                </form>
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
    // Close modals on clicking background
    window.onclick = function(event) {
        if (event.target.classList.contains('inst-modal')) {
            event.target.classList.remove('active');
        }
    }
    // Auto-reopen modal-details if there are validation errors
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openInstModal('modal-details');
        });
    @endif
</script>
@endsection
