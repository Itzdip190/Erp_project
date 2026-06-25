@extends('layouts.app')

@section('title', 'Fee Basics')

@section('styles')
<style>
    /* Premium Blue & White Theme Overrides */
    .basics-container {
        font-family: 'Inter', sans-serif;
        background: #f4f6f9;
        padding: 4px;
        color: #1e293b;
    }
    
    /* Header Section */
    .hdr-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(37,99,235,0.06);
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 5px solid #2563eb;
    }
    .hdr-title-wrap h1 {
        font-size: 22px;
        font-weight: 800;
        color: #1e3a8a;
        margin: 0 0 4px 0;
    }
    .hdr-title-wrap p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }
    
    /* Academic Year Dropdown & Button */
    .academic-filter-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .session-selector-box {
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        cursor: pointer;
        position: relative;
    }
    .session-selector-box label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #2563eb;
        margin-bottom: 2px;
    }
    .session-selector-box select {
        background: transparent;
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        padding: 0 16px 0 0;
        cursor: pointer;
        -webkit-appearance: none;
    }
    .session-selector-box::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 10px;
        color: #64748b;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-20%);
        pointer-events: none;
    }

    /* KPI Row */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .kpi-card {
        border-radius: 12px;
        padding: 16px;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 70px;
        opacity: 0.15;
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
    }
    .kpi-card.schedules { background: #10b981; } /* Green */
    .kpi-card.schedules::after { content: '\f073'; }
    .kpi-card.components { background: #ef4444; } /* Red */
    .kpi-card.components::after { content: '\f0f2'; }
    .kpi-card.discounts { background: #d97706; } /* Orange */
    .kpi-card.discounts::after { content: '\f02d'; }
    .kpi-card.misc { background: #b45309; } /* Gold/Brown */
    .kpi-card.misc::after { content: '\f013'; }
    .kpi-card.fines { background: #6b7280; } /* Gray */
    .kpi-card.fines::after { content: '\f53d'; }
    
    .kpi-value {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
    }
    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        opacity: 0.95;
    }

    /* Section Cards */
    .section-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-hdr {
        background: #ffffff;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-hdr h2 {
        font-size: 15px;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-hdr-actions {
        display: flex;
        gap: 10px;
    }

    /* Professional Tables */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    table.fee-table {
        width: 100%;
        border-collapse: collapse;
    }
    table.fee-table th {
        background: #0f3a4c; /* Dark teal/blue from screenshot */
        color: #ffffff;
        padding: 12px 18px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
    }
    table.fee-table td {
        padding: 14px 18px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    table.fee-table tr:hover td {
        background: #f8fafc;
    }
    .row-index {
        color: #94a3b8;
        font-size: 11px;
        margin-right: 6px;
    }

    /* Action Buttons */
    .btn-action-edit {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 6px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-action-edit:hover {
        background: #2563eb;
        color: #ffffff;
    }
    .btn-action-delete {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        border-radius: 6px;
        padding: 6px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 4px;
    }
    .btn-action-delete:hover {
        background: #ef4444;
        color: #ffffff;
    }

    /* Toggle Switch iOS style */
    .switch-label {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
    }
    .switch-label input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 22px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(22px);
    }

    /* Modal dialog */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
    }
    .modal-content-custom {
        background: #ffffff;
        border-radius: 12px;
        width: 500px;
        max-width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        overflow: hidden;
        animation: scaleIn 0.25s ease-out;
    }
    .modal-hdr {
        background: #1e3a8a;
        color: #ffffff;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-hdr h3 { margin: 0; font-size: 15px; font-weight: 700; }
    .modal-close { background: none; border: none; color: #ffffff; font-size: 18px; cursor: pointer; }
    .modal-body-custom { padding: 20px; }
    .modal-ftr {
        background: #f8fafc;
        padding: 12px 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Slideout Drawer */
    .drawer-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        z-index: 999;
        display: none;
        backdrop-filter: blur(2px);
    }
    .drawer-content {
        position: fixed;
        top: 0; right: -600px;
        width: 550px;
        height: 100vh;
        background: #ffffff;
        z-index: 1000;
        box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }
    .drawer-content.open { right: 0; }
    .drawer-hdr {
        background: #ea580c; /* Orange Header color from screenshot */
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .drawer-hdr h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .drawer-close { background: none; border: none; color: #ffffff; font-size: 20px; cursor: pointer; }
    .drawer-body { padding: 24px; flex: 1; overflow-y: auto; }
    .drawer-ftr {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Multi-step progress bar */
    .step-progress-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 24px;
        position: relative;
        padding: 0 10px;
    }
    .step-progress-line {
        position: absolute;
        top: 14px; left: 30px; right: 30px;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }
    .step-progress-line-fill {
        position: absolute;
        top: 14px; left: 30px;
        height: 2px;
        width: 0%;
        background: #10b981;
        z-index: 2;
        transition: width 0.3s ease;
    }
    .step-indicator {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: #64748b;
        transition: all 0.3s;
    }
    .step-indicator.active .step-circle {
        border-color: #f59e0b;
        color: #f59e0b;
    }
    .step-indicator.completed .step-circle {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }
    .step-label {
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        max-width: 140px;
        text-align: center;
    }
    .step-indicator.active .step-label { color: #f59e0b; }
    .step-indicator.completed .step-label { color: #10b981; }

    /* Helper styles */
    .classes-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        max-height: 300px;
        overflow-y: auto;
        padding: 8px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .classes-grid label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        transition: background 0.15s;
    }
    .classes-grid label:hover {
        background: #f1f5f9;
    }

    @keyframes scaleIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="basics-container">
    
    <!-- 1. Header Card -->
    <div class="hdr-card">
        <div class="hdr-title-wrap">
            <h1>Fee Basics</h1>
            <p>Configure academic years, fee structures, component heads, discounts, and fine criteria.</p>
        </div>
        
        <div class="academic-filter-wrap">
            <!-- Academic Session Filter -->
            <div class="session-selector-box" onclick="this.querySelector('select').focus()">
                <label>Academic Year *</label>
                <form id="sessionFilterForm" method="GET" action="{{ route('school.fees.basics') }}">
                    <select name="academic_session_id" onchange="document.getElementById('sessionFilterForm').submit()">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $selectedSession->id == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <!-- Add Academic Year Button -->
            <button class="btn btn-primary" onclick="openModal('academicModal')">
                <i class="fas fa-plus"></i> ADD ACADEMIC YEAR
            </button>
        </div>
    </div>

    <!-- 2. KPI Metrics Counters Row -->
    <div class="kpi-row">
        <div class="kpi-card schedules">
            <div>
                <div class="kpi-value">{{ $schedulesCount }}</div>
                <div class="kpi-label">No. of Fee schedule created</div>
            </div>
        </div>
        <div class="kpi-card components">
            <div>
                <div class="kpi-value">{{ $componentsCount }}</div>
                <div class="kpi-label">No. of Fee component created</div>
            </div>
        </div>
        <div class="kpi-card discounts">
            <div>
                <div class="kpi-value">{{ $discountsCount }}</div>
                <div class="kpi-label">No. of Fee discounts created</div>
            </div>
        </div>
        <div class="kpi-card misc">
            <div>
                <div class="kpi-value">{{ $miscFeesCount }}</div>
                <div class="kpi-label">No. of Misc Fee created</div>
            </div>
        </div>
        <div class="kpi-card fines">
            <div>
                <div class="kpi-value">{{ $finesCount }}</div>
                <div class="kpi-label">No. of Fee fine created</div>
            </div>
        </div>
    </div>

    <!-- 3. Fee Schedule Panel -->
    <div class="section-card">
        <div class="section-hdr">
            <h2>1. Fee Schedule</h2>
            <div class="section-hdr-actions">
                <button class="btn btn-outline" style="border-color:#d97706; color:#d97706;" onclick="openDrawer('scheduleDrawer')">
                    <i class="fas fa-plus"></i> ADD FEE SCHEDULE
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Classes</th>
                        <th>No. of Installments</th>
                        <th>Schedule Name</th>
                        <th>Date</th>
                        <th style="width: 100px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $idx => $sched)
                        <tr>
                            <td>
                                <span class="row-index">{{ sprintf('%02d', $idx + 1) }}.</span>
                                {{ $sched->classes }}
                            </td>
                            <td>{{ $sched->no_of_installments }}</td>
                            <td><strong>{{ $sched->name }}</strong></td>
                            <td>{{ $sched->start_date->format('d/m/Y') }} - {{ $sched->end_date->format('d/m/Y') }}</td>
                            <td style="text-align: center;">
                                <form action="{{ route('school.fees.basics') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="type" value="schedule">
                                    <input type="hidden" name="id" value="{{ $sched->id }}">
                                    <button type="submit" class="btn-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 24px;">No Fee Schedules created for this session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Fee Component Panel -->
    <div class="section-card">
        <div class="section-hdr">
            <h2>2. Fee Component</h2>
            <div class="section-hdr-actions">
                <button class="btn btn-outline" style="border-color:#1e3a8a; color:#1e3a8a;" onclick="showToast('Reordering is locked to default layout.')">
                    <i class="fas fa-arrows-alt-v"></i> COMPONENT REORDERING
                </button>
                <button class="btn btn-outline" style="border-color:#ea580c; color:#ea580c;" onclick="openModal('componentModal')">
                    <i class="fas fa-plus"></i> ADD FEE COMPONENT
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Head Name</th>
                        <th>Component Name</th>
                        <th>Admission Type</th>
                        <th>Gender</th>
                        <th style="width: 100px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($components as $idx => $comp)
                        <tr>
                            <td>
                                <span class="row-index">{{ sprintf('%02d', $idx + 1) }}.</span>
                                <strong>{{ $comp->head_name }}</strong>
                            </td>
                            <td>{{ $comp->component_name }}</td>
                            <td><span class="badge badge-blue">{{ $comp->admission_type }}</span></td>
                            <td><span class="badge badge-purple">{{ $comp->gender }}</span></td>
                            <td style="text-align: center;">
                                <form action="{{ route('school.fees.basics') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="type" value="component">
                                    <input type="hidden" name="id" value="{{ $comp->id }}">
                                    <button type="submit" class="btn-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 24px;">No Fee Components created for this session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Fee Discounts Panel -->
    <div class="section-card">
        <div class="section-hdr">
            <h2>3. Fee Discounts</h2>
            <div class="section-hdr-actions">
                <button class="btn btn-outline" style="border-color:#ea580c; color:#ea580c;" onclick="openDrawer('discountDrawer')">
                    <i class="fas fa-plus"></i> ADD FEE DISCOUNTS
                </button>
                <button class="btn btn-outline" style="border-color:#64748b; color:#64748b;" onclick="showToast('Discount change logs are empty.')">
                    SHOW LOGS
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Discount Name</th>
                        <th>Remarks</th>
                        <th>Amount</th>
                        <th style="width: 100px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $idx => $disc)
                        <tr>
                            <td>
                                <span class="row-index">{{ sprintf('%02d', $idx + 1) }}.</span>
                                <strong>{{ $disc->name }}</strong>
                            </td>
                            <td>{{ $disc->remarks ?? '—' }}</td>
                            <td>₹{{ number_format($disc->amount, 2) }}</td>
                            <td style="text-align: center;">
                                <form action="{{ route('school.fees.basics') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discount?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="type" value="discount">
                                    <input type="hidden" name="id" value="{{ $disc->id }}">
                                    <button type="submit" class="btn-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">No Fee Discounts created for this session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. Misc Fee Panel -->
    <div class="section-card">
        <div class="section-hdr">
            <h2>4. Misc. Fee</h2>
            <div class="section-hdr-actions">
                <button class="btn btn-outline" style="border-color:#ea580c; color:#ea580c;" onclick="openDrawer('miscDrawer')">
                    <i class="fas fa-plus"></i> ADD MISC. FEE
                </button>
                <button class="btn btn-outline" style="border-color:#64748b; color:#64748b;" onclick="showToast('Misc fee logs are empty.')">
                    SHOW LOGS
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Fee Name</th>
                        <th>Remarks</th>
                        <th>Amount</th>
                        <th style="width: 100px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($miscFees as $idx => $mfee)
                        <tr>
                            <td>
                                <span class="row-index">{{ sprintf('%02d', $idx + 1) }}.</span>
                                <strong>{{ $mfee->name }}</strong>
                            </td>
                            <td>{{ $mfee->remarks ?? '—' }}</td>
                            <td>₹{{ number_format($mfee->amount, 2) }}</td>
                            <td style="text-align: center;">
                                <form action="{{ route('school.fees.basics') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this misc fee?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="type" value="misc_fee">
                                    <input type="hidden" name="id" value="{{ $mfee->id }}">
                                    <button type="submit" class="btn-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">No Miscellaneous Fees created for this session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 7. Fee Fine Panel -->
    <div class="section-card">
        <div class="section-hdr">
            <h2>5. Fee fine</h2>
            <div class="section-hdr-actions">
                <button class="btn btn-outline" style="border-color:#ea580c; color:#ea580c;" onclick="openModal('fineModal')">
                    <i class="fas fa-plus"></i> ADD FEE FINE
                </button>
                <button class="btn btn-outline" style="border-color:#64748b; color:#64748b;" onclick="showToast('Fine adjustment logs are empty.')">
                    SHOW LOGS
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Fine Name</th>
                        <th>Fine Type</th>
                        <th>Fine Status</th>
                        <th style="width: 100px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fines as $idx => $fine)
                        <tr>
                            <td>
                                <span class="row-index">{{ sprintf('%02d', $idx + 1) }}.</span>
                                <strong>{{ $fine->name }}</strong> (₹{{ number_format($fine->fine_amount, 2) }})
                            </td>
                            <td>{{ $fine->fine_type }}</td>
                            <td>
                                <label class="switch-label">
                                    <input type="checkbox" class="fine-status-toggle" data-id="{{ $fine->id }}" {{ $fine->status ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('school.fees.basics') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this fine?')">
                                    @csrf
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="type" value="fine">
                                    <input type="hidden" name="id" value="{{ $fine->id }}">
                                    <button type="submit" class="btn-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">No Fines created for this session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD ACADEMIC YEAR
     ============================================== -->
<div class="modal-overlay" id="academicModal" onclick="closeModalOnOutsideClick(event, 'academicModal')">
    <div class="modal-content-custom">
        <div class="modal-hdr">
            <h3>Create Academic Session</h3>
            <button class="modal-close" onclick="closeModal('academicModal')">&times;</button>
        </div>
        <form action="{{ route('school.fees.basics') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="add_academic_session">
            <div class="modal-body-custom">
                <div class="form-group">
                    <label class="form-label">Session Name / Label *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Apr 2025 - Mar 2026" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date *</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 15px;">
                    <input type="checkbox" name="is_current" id="is_current" value="1" style="width:16px; height:16px;">
                    <label for="is_current" style="margin:0; font-weight:600; cursor:pointer;">Set as Active / Current Session</label>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn btn-outline" onclick="closeModal('academicModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Session</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD FEE COMPONENT
     ============================================== -->
<div class="modal-overlay" id="componentModal" onclick="closeModalOnOutsideClick(event, 'componentModal')">
    <div class="modal-content-custom">
        <div class="modal-hdr">
            <h3>Add Fee Component</h3>
            <button class="modal-close" onclick="closeModal('componentModal')">&times;</button>
        </div>
        <form action="{{ route('school.fees.basics') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="add_fee_component">
            <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
            <div class="modal-body-custom">
                <div class="form-group">
                    <label class="form-label">Head Name *</label>
                    <input type="text" name="head_name" class="form-control" placeholder="e.g. School Fee" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Component Name *</label>
                    <input type="text" name="component_name" class="form-control" placeholder="e.g. Transport Fee" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Admission Type *</label>
                    <select name="admission_type" class="form-control">
                        <option value="All Students">All Students</option>
                        <option value="New">New Admission Only</option>
                        <option value="Existing">Existing Students Only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender Restriction *</label>
                    <select name="gender" class="form-control">
                        <option value="All Students">All Students</option>
                        <option value="Male">Male Only</option>
                        <option value="Female">Female Only</option>
                    </select>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn btn-outline" onclick="closeModal('componentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Component</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD FEE FINE
     ============================================== -->
<div class="modal-overlay" id="fineModal" onclick="closeModalOnOutsideClick(event, 'fineModal')">
    <div class="modal-content-custom">
        <div class="modal-hdr">
            <h3>Add Fee Fine Policy</h3>
            <button class="modal-close" onclick="closeModal('fineModal')">&times;</button>
        </div>
        <form action="{{ route('school.fees.basics') }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="add_fee_fine">
            <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
            <div class="modal-body-custom">
                <div class="form-group">
                    <label class="form-label">Fine Policy Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. late fine" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Fine Type *</label>
                    <select name="fine_type" class="form-control">
                        <option value="Fixed Amount">Fixed Amount</option>
                        <option value="Daily">Daily Increment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fine Amount (₹) *</label>
                    <input type="number" step="0.01" name="fine_amount" class="form-control" placeholder="e.g. 250" required>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn btn-outline" onclick="closeModal('fineModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Fine</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     DRAWER: CREATE SCHEDULE (MULTI-STEP)
     ============================================== -->
<div class="drawer-overlay" id="scheduleDrawerOverlay" onclick="closeDrawer('scheduleDrawer')"></div>
<div class="drawer-content" id="scheduleDrawer">
    <div class="drawer-hdr">
        <h3><i class="fas fa-calendar-plus"></i> Create Schedule</h3>
        <button class="drawer-close" onclick="closeDrawer('scheduleDrawer')">&times;</button>
    </div>
    <form action="{{ route('school.fees.basics') }}" method="POST" id="scheduleForm">
        @csrf
        <input type="hidden" name="action" value="add_fee_schedule">
        <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
        
        <div class="drawer-body">
            <!-- Multi-step Indicator -->
            <div class="step-progress-bar">
                <div class="step-progress-line"></div>
                <div class="step-progress-line-fill" id="sched-progress-fill"></div>
                
                <div class="step-indicator active" id="sched-ind-1">
                    <div class="step-circle"><i class="fas fa-check" style="display:none;"></i><span class="num">1</span></div>
                    <div class="step-label">Select Classes</div>
                </div>
                <div class="step-indicator" id="sched-ind-2">
                    <div class="step-circle"><span class="num">2</span></div>
                    <div class="step-label">Installments & Name</div>
                </div>
            </div>

            <!-- Wizard Step 1: Classes Checklist -->
            <div class="wizard-step" id="sched-step-1">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:12px;">Select classes for which you want to apply the fee schedule</h4>
                
                <div style="margin-bottom:10px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; cursor:pointer;">
                        <input type="checkbox" id="selectAllClasses" onchange="toggleSelectAllClasses(this)">
                        SELECT ALL
                    </label>
                </div>
                
                <div class="classes-grid">
                    @foreach($classes as $cls)
                        <label>
                            <input type="checkbox" name="classes[]" value="{{ $cls->name }}" class="class-checkbox">
                            {{ $cls->name }}
                        </label>
                    @endforeach
                    @if($classes->isEmpty())
                        <div style="grid-column: span 2; color:#94a3b8; text-align:center; padding: 20px;">No Classes available in assignments.</div>
                    @endif
                </div>
            </div>

            <!-- Wizard Step 2: Installments & General Info -->
            <div class="wizard-step" id="sched-step-2" style="display:none;">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:16px;">Add Installments and General Details</h4>
                
                <div class="form-group">
                    <label class="form-label">Schedule Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. fees schedule 1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Number of Installments *</label>
                    <input type="number" min="1" max="12" name="no_of_installments" class="form-control" placeholder="e.g. 4" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date *</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="drawer-ftr">
            <button type="button" class="btn btn-outline" id="sched-btn-back" style="display:none;" onclick="navigateSchedStep(1)">Back</button>
            <div style="flex: 1;"></div>
            <button type="button" class="btn btn-accent" id="sched-btn-next" onclick="navigateSchedStep(2)">Next &rarr;</button>
            <button type="submit" class="btn btn-success" id="sched-btn-submit" style="display:none;">Create Schedule</button>
        </div>
    </form>
</div>

<!-- ==============================================
     DRAWER: CREATE DISCOUNT (MULTI-STEP)
     ============================================== -->
<div class="drawer-overlay" id="discountDrawerOverlay" onclick="closeDrawer('discountDrawer')"></div>
<div class="drawer-content" id="discountDrawer">
    <div class="drawer-hdr">
        <h3><i class="fas fa-tag"></i> Create Discount</h3>
        <button class="drawer-close" onclick="closeDrawer('discountDrawer')">&times;</button>
    </div>
    <form action="{{ route('school.fees.basics') }}" method="POST" id="discountForm">
        @csrf
        <input type="hidden" name="action" value="add_fee_discount">
        <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
        
        <div class="drawer-body">
            <!-- Multi-step Indicator -->
            <div class="step-progress-bar">
                <div class="step-progress-line"></div>
                <div class="step-progress-line-fill" id="disc-progress-fill"></div>
                
                <div class="step-indicator active" id="disc-ind-1">
                    <div class="step-circle"><span class="num">1</span></div>
                    <div class="step-label">Classes</div>
                </div>
                <div class="step-indicator" id="disc-ind-2">
                    <div class="step-circle"><span class="num">2</span></div>
                    <div class="step-label">Discount Details</div>
                </div>
                <div class="step-indicator" id="disc-ind-3">
                    <div class="step-circle"><span class="num">3</span></div>
                    <div class="step-label">Select Students</div>
                </div>
            </div>

            <!-- Wizard Step 1: Classes Selection -->
            <div class="wizard-step" id="disc-step-1">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:12px;">Select Classes applicable for this discount</h4>
                <div style="margin-bottom:10px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; cursor:pointer;">
                        <input type="checkbox" id="selectAllDiscClasses" onchange="toggleSelectAllDiscClasses(this)">
                        SELECT ALL
                    </label>
                </div>
                <div class="classes-grid">
                    @foreach($classes as $cls)
                        <label>
                            <input type="checkbox" name="classes[]" value="{{ $cls->name }}" class="disc-class-checkbox">
                            {{ $cls->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Wizard Step 2: Discount Details -->
            <div class="wizard-step" id="disc-step-2" style="display:none;">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:16px;">Add Discount Policy Information</h4>
                <div class="form-group">
                    <label class="form-label">Discount Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Sibling Discount" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Remarks / Description</label>
                    <textarea name="remarks" class="form-control" placeholder="Remarks related to this discount eligibility" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Amount (₹) *</label>
                    <input type="number" name="amount" class="form-control" placeholder="e.g. 500" required>
                </div>
            </div>

            <!-- Wizard Step 3: Students Selection -->
            <div class="wizard-step" id="disc-step-3" style="display:none;">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:12px;">Select targeted students (Leave empty for All Students)</h4>
                <div class="classes-grid" style="max-height: 350px;">
                    @foreach($students as $stud)
                        <label>
                            <input type="checkbox" name="student_ids[]" value="{{ $stud->id }}">
                            {{ $stud->name }} (Class: {{ $stud->class?->name ?? 'N/A' }} - Adm: {{ $stud->admission_no }})
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="drawer-ftr">
            <button type="button" class="btn btn-outline" id="disc-btn-back" style="display:none;" onclick="navigateDiscStep(discCurrentStep - 1)">Back</button>
            <div style="flex: 1;"></div>
            <button type="button" class="btn btn-accent" id="disc-btn-next" onclick="navigateDiscStep(discCurrentStep + 1)">Next &rarr;</button>
            <button type="submit" class="btn btn-success" id="disc-btn-submit" style="display:none;">Create Discount</button>
        </div>
    </form>
</div>

<!-- ==============================================
     DRAWER: CREATE MISC FEE (MULTI-STEP)
     ============================================== -->
<div class="drawer-overlay" id="miscDrawerOverlay" onclick="closeDrawer('miscDrawer')"></div>
<div class="drawer-content" id="miscDrawer">
    <div class="drawer-hdr">
        <h3><i class="fas fa-cogs"></i> Create Misc Fee</h3>
        <button class="drawer-close" onclick="closeDrawer('miscDrawer')">&times;</button>
    </div>
    <form action="{{ route('school.fees.basics') }}" method="POST" id="miscForm">
        @csrf
        <input type="hidden" name="action" value="add_misc_fee">
        <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
        
        <div class="drawer-body">
            <!-- Multi-step Indicator -->
            <div class="step-progress-bar">
                <div class="step-progress-line"></div>
                <div class="step-progress-line-fill" id="misc-progress-fill"></div>
                
                <div class="step-indicator active" id="misc-ind-1">
                    <div class="step-circle"><span class="num">1</span></div>
                    <div class="step-label">Classes</div>
                </div>
                <div class="step-indicator" id="misc-ind-2">
                    <div class="step-circle"><span class="num">2</span></div>
                    <div class="step-label">Fee Details</div>
                </div>
                <div class="step-indicator" id="misc-ind-3">
                    <div class="step-circle"><span class="num">3</span></div>
                    <div class="step-label">Select Students</div>
                </div>
            </div>

            <!-- Wizard Step 1: Classes Selection -->
            <div class="wizard-step" id="misc-step-1">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:12px;">Select Classes applicable for this miscellaneous fee</h4>
                <div style="margin-bottom:10px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; cursor:pointer;">
                        <input type="checkbox" id="selectAllMiscClasses" onchange="toggleSelectAllMiscClasses(this)">
                        SELECT ALL
                    </label>
                </div>
                <div class="classes-grid">
                    @foreach($classes as $cls)
                        <label>
                            <input type="checkbox" name="classes[]" value="{{ $cls->name }}" class="misc-class-checkbox">
                            {{ $cls->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Wizard Step 2: Fee Details -->
            <div class="wizard-step" id="misc-step-2" style="display:none;">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:16px;">Add Miscellaneous Fee Information</h4>
                <div class="form-group">
                    <label class="form-label">Fee Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Exam Sheet Fee" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Remarks / Description</label>
                    <textarea name="remarks" class="form-control" placeholder="Description of this charge" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Fee Amount (₹) *</label>
                    <input type="number" name="amount" class="form-control" placeholder="e.g. 150" required>
                </div>
            </div>

            <!-- Wizard Step 3: Students Selection -->
            <div class="wizard-step" id="misc-step-3" style="display:none;">
                <h4 style="font-size:14px; font-weight:700; color:#334155; margin-bottom:12px;">Select targeted students (Leave empty for All Students)</h4>
                <div class="classes-grid" style="max-height: 350px;">
                    @foreach($students as $stud)
                        <label>
                            <input type="checkbox" name="student_ids[]" value="{{ $stud->id }}">
                            {{ $stud->name }} (Class: {{ $stud->class?->name ?? 'N/A' }} - Adm: {{ $stud->admission_no }})
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="drawer-ftr">
            <button type="button" class="btn btn-outline" id="misc-btn-back" style="display:none;" onclick="navigateMiscStep(miscCurrentStep - 1)">Back</button>
            <div style="flex: 1;"></div>
            <button type="button" class="btn btn-accent" id="misc-btn-next" onclick="navigateMiscStep(miscCurrentStep + 1)">Next &rarr;</button>
            <button type="submit" class="btn btn-success" id="misc-btn-submit" style="display:none;">Create Fee</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // --- Modal Helpers ---
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function closeModalOnOutsideClick(event, modalId) {
        if (event.target.id === modalId) {
            closeModal(modalId);
        }
    }

    // --- Drawer Helpers ---
    function openDrawer(drawerId) {
        document.getElementById(drawerId + 'Overlay').style.display = 'block';
        document.getElementById(drawerId).classList.add('open');
    }

    function closeDrawer(drawerId) {
        document.getElementById(drawerId + 'Overlay').style.display = 'none';
        document.getElementById(drawerId).classList.remove('open');
    }

    // --- Create Schedule Multi-Step Wizard ---
    let schedCurrentStep = 1;
    function navigateSchedStep(step) {
        if (step === 2) {
            // Validate step 1
            const checkedClasses = document.querySelectorAll('.class-checkbox:checked');
            if (checkedClasses.length === 0) {
                alert('Please select at least one class.');
                return;
            }
            // Transition indicators
            document.getElementById('sched-ind-1').classList.remove('active');
            document.getElementById('sched-ind-1').classList.add('completed');
            document.getElementById('sched-ind-1').querySelector('.num').style.display = 'none';
            
            document.getElementById('sched-ind-2').classList.add('active');
            
            // Toggle panels
            document.getElementById('sched-step-1').style.display = 'none';
            document.getElementById('sched-step-2').style.display = 'block';
            
            // Buttons
            document.getElementById('sched-btn-back').style.display = 'inline-block';
            document.getElementById('sched-btn-next').style.display = 'none';
            document.getElementById('sched-btn-submit').style.display = 'inline-block';
            
            document.getElementById('sched-progress-fill').style.width = '100%';
            
            schedCurrentStep = 2;
        } else if (step === 1) {
            // Transition indicators back
            document.getElementById('sched-ind-1').classList.add('active');
            document.getElementById('sched-ind-1').classList.remove('completed');
            document.getElementById('sched-ind-1').querySelector('.num').style.display = 'inline-block';
            
            document.getElementById('sched-ind-2').classList.remove('active');
            
            // Toggle panels back
            document.getElementById('sched-step-1').style.display = 'block';
            document.getElementById('sched-step-2').style.display = 'none';
            
            // Buttons
            document.getElementById('sched-btn-back').style.display = 'none';
            document.getElementById('sched-btn-next').style.display = 'inline-block';
            document.getElementById('sched-btn-submit').style.display = 'none';
            
            document.getElementById('sched-progress-fill').style.width = '0%';
            
            schedCurrentStep = 1;
        }
    }
    
    function toggleSelectAllClasses(elem) {
        document.querySelectorAll('.class-checkbox').forEach(cb => cb.checked = elem.checked);
    }

    // --- Create Discount Multi-Step Wizard ---
    let discCurrentStep = 1;
    function navigateDiscStep(step) {
        if (step === 2) {
            // Validate step 1
            const checkedClasses = document.querySelectorAll('.disc-class-checkbox:checked');
            if (checkedClasses.length === 0) {
                alert('Please select at least one class.');
                return;
            }
            document.getElementById('disc-ind-1').classList.remove('active');
            document.getElementById('disc-ind-1').classList.add('completed');
            document.getElementById('disc-ind-2').classList.add('active');
            document.getElementById('disc-ind-3').classList.remove('active');
            
            document.getElementById('disc-step-1').style.display = 'none';
            document.getElementById('disc-step-2').style.display = 'block';
            document.getElementById('disc-step-3').style.display = 'none';
            
            document.getElementById('disc-btn-back').style.display = 'inline-block';
            document.getElementById('disc-btn-next').style.display = 'inline-block';
            document.getElementById('disc-btn-submit').style.display = 'none';
            
            document.getElementById('disc-progress-fill').style.width = '50%';
            discCurrentStep = 2;
        } else if (step === 3) {
            // Validate step 2
            const discountName = document.querySelector('#disc-step-2 input[name="name"]').value;
            const discountAmount = document.querySelector('#disc-step-2 input[name="amount"]').value;
            if (!discountName || !discountAmount) {
                alert('Please fill out the discount details.');
                return;
            }
            document.getElementById('disc-ind-2').classList.remove('active');
            document.getElementById('disc-ind-2').classList.add('completed');
            document.getElementById('disc-ind-3').classList.add('active');
            
            document.getElementById('disc-step-2').style.display = 'none';
            document.getElementById('disc-step-3').style.display = 'block';
            
            document.getElementById('disc-btn-back').style.display = 'inline-block';
            document.getElementById('disc-btn-next').style.display = 'none';
            document.getElementById('disc-btn-submit').style.display = 'inline-block';
            
            document.getElementById('disc-progress-fill').style.width = '100%';
            discCurrentStep = 3;
        } else if (step === 1) {
            document.getElementById('disc-ind-1').classList.add('active');
            document.getElementById('disc-ind-1').classList.remove('completed');
            document.getElementById('disc-ind-2').classList.remove('active');
            
            document.getElementById('disc-step-1').style.display = 'block';
            document.getElementById('disc-step-2').style.display = 'none';
            
            document.getElementById('disc-btn-back').style.display = 'none';
            document.getElementById('disc-btn-next').style.display = 'inline-block';
            document.getElementById('disc-btn-submit').style.display = 'none';
            
            document.getElementById('disc-progress-fill').style.width = '0%';
            discCurrentStep = 1;
        }
    }
    function toggleSelectAllDiscClasses(elem) {
        document.querySelectorAll('.disc-class-checkbox').forEach(cb => cb.checked = elem.checked);
    }

    // --- Create Misc Fee Multi-Step Wizard ---
    let miscCurrentStep = 1;
    function navigateMiscStep(step) {
        if (step === 2) {
            // Validate step 1
            const checkedClasses = document.querySelectorAll('.misc-class-checkbox:checked');
            if (checkedClasses.length === 0) {
                alert('Please select at least one class.');
                return;
            }
            document.getElementById('misc-ind-1').classList.remove('active');
            document.getElementById('misc-ind-1').classList.add('completed');
            document.getElementById('misc-ind-2').classList.add('active');
            document.getElementById('misc-ind-3').classList.remove('active');
            
            document.getElementById('misc-step-1').style.display = 'none';
            document.getElementById('misc-step-2').style.display = 'block';
            document.getElementById('misc-step-3').style.display = 'none';
            
            document.getElementById('misc-btn-back').style.display = 'inline-block';
            document.getElementById('misc-btn-next').style.display = 'inline-block';
            document.getElementById('misc-btn-submit').style.display = 'none';
            
            document.getElementById('misc-progress-fill').style.width = '50%';
            miscCurrentStep = 2;
        } else if (step === 3) {
            // Validate step 2
            const miscName = document.querySelector('#misc-step-2 input[name="name"]').value;
            const miscAmount = document.querySelector('#misc-step-2 input[name="amount"]').value;
            if (!miscName || !miscAmount) {
                alert('Please fill out the fee details.');
                return;
            }
            document.getElementById('misc-ind-2').classList.remove('active');
            document.getElementById('misc-ind-2').classList.add('completed');
            document.getElementById('misc-ind-3').classList.add('active');
            
            document.getElementById('misc-step-2').style.display = 'none';
            document.getElementById('misc-step-3').style.display = 'block';
            
            document.getElementById('misc-btn-back').style.display = 'inline-block';
            document.getElementById('misc-btn-next').style.display = 'none';
            document.getElementById('misc-btn-submit').style.display = 'inline-block';
            
            document.getElementById('misc-progress-fill').style.width = '100%';
            miscCurrentStep = 3;
        } else if (step === 1) {
            document.getElementById('misc-ind-1').classList.add('active');
            document.getElementById('misc-ind-1').classList.remove('completed');
            document.getElementById('misc-ind-2').classList.remove('active');
            
            document.getElementById('misc-step-1').style.display = 'block';
            document.getElementById('misc-step-2').style.display = 'none';
            
            document.getElementById('misc-btn-back').style.display = 'none';
            document.getElementById('misc-btn-next').style.display = 'inline-block';
            document.getElementById('misc-btn-submit').style.display = 'none';
            
            document.getElementById('misc-progress-fill').style.width = '0%';
            miscCurrentStep = 1;
        }
    }
    function toggleSelectAllMiscClasses(elem) {
        document.querySelectorAll('.misc-class-checkbox').forEach(cb => cb.checked = elem.checked);
    }

    // --- AJAX Toggle Status for Fines ---
    $(document).ready(function() {
        $('.fine-status-toggle').on('change', function() {
            const fineId = $(this).data('id');
            const isChecked = $(this).is(':checked');
            
            $.ajax({
                url: "{{ route('school.fees.basics') }}",
                type: "POST",
                data: {
                    action: "toggle_fine_status",
                    id: fineId
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Fine policy status toggled successfully.');
                    } else {
                        showToast('Failed to update fine status.');
                    }
                },
                error: function() {
                    showToast('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
@endsection
