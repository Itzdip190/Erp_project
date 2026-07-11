@extends('layouts.app')

@section('title', 'Transport Fee Schedules')

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
        background: #0f3a4c;
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

    /* Modal Overlay & Card styling */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        z-index: 9999;
        display: none;
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
    }
    .modal-content-custom {
        background: #ffffff;
        border-radius: 12px;
        width: 580px;
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
    .modal-body-custom { padding: 20px; max-height: 70vh; overflow-y: auto; }
    .modal-ftr {
        background: #f8fafc;
        padding: 12px 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
        font-size: 13px;
    }

    .months-container {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        background: #f8fafc;
        max-height: 250px;
        overflow-y: auto;
    }
    .month-row {
        display: grid;
        grid-template-columns: 40px 1fr 180px;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .month-row:last-child {
        border-bottom: none;
    }

    @keyframes scaleIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    body.dark-mode .basics-container {
        background: #0f172a !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .hdr-card,
    body.dark-mode .section-card,
    body.dark-mode .section-hdr,
    body.dark-mode .modal-content-custom,
    body.dark-mode .modal-ftr,
    body.dark-mode table.fee-table td,
    body.dark-mode .months-container {
        background: #111827 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .hdr-title-wrap h1,
    body.dark-mode .section-hdr h2,
    body.dark-mode .modal-hdr h3,
    body.dark-mode .session-selector-box select,
    body.dark-mode .form-control {
        color: #f8fafc !important;
    }
    body.dark-mode .session-selector-box {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }
    body.dark-mode table.fee-table tr:hover td {
        background: rgba(255, 255, 255, 0.04) !important;
    }
</style>
@endsection

@section('content')
<div class="basics-container">
    
    <!-- Header Card -->
    <div class="hdr-card">
        <div class="hdr-title-wrap">
            <h1>Transport Fee Schedules</h1>
            <p>Set up repeating monthly billable schedules for routes and default school-wide transport plans.</p>
        </div>
        
        <div class="academic-filter-wrap">
            <div class="session-selector-box" onclick="this.querySelector('select').focus()">
                <label>Academic Year *</label>
                <form id="sessionFilterForm" method="GET" action="{{ route('school.transport.fee-schedules') }}">
                    <select name="academic_session_id" onchange="document.getElementById('sessionFilterForm').submit()">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $selectedSession->id == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> ADD TRANSPORT SCHEDULE
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Schedules Table Card -->
    <div class="section-card">
        <div class="section-hdr">
            <h2>Transport Schedules ({{ $selectedSession->name }})</h2>
        </div>
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Schedule Name</th>
                        <th>Route Mapping</th>
                        <th>Ref Rate (Pick + Drop)</th>
                        <th>Billable Months</th>
                        <th>Status</th>
                        <th style="width: 120px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $idx => $sched)
                        <tr>
                            <td><span class="row-index">{{ sprintf('%02d', $idx + 1) }}.</span></td>
                            <td><strong>{{ $sched->name }}</strong></td>
                            <td>
                                @if($sched->route)
                                    <span style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; border-radius:6px; padding:2px 8px; font-size:0.8rem; font-weight:600;">
                                        {{ $sched->route->name }}
                                    </span>
                                @else
                                    <span style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:6px; padding:2px 8px; font-size:0.8rem; font-weight:600;">
                                        School-wide Default
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($sched->route)
                                    ₹{{ number_format($sched->route->total_fare, 2) }}
                                @else
                                    <span style="color:#94a3b8; font-style:italic;">Variable per route</span>
                                @endif
                            </td>
                            <td>
                                {{ is_array($sched->installments) ? count($sched->installments) : 0 }} Installments 
                                <span style="font-size: 11px; color:#64748b; display: block; margin-top: 4px;">
                                    ({{ collect($sched->installments)->map(fn($i) => $i['name'] ?? $i['label'] ?? '')->filter()->implode(', ') }})
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $sched->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $sched->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="text-align: center; display: flex; justify-content: center; gap: 6px;">
                                <button class="btn-action-edit" onclick="openEditModal({{ json_encode($sched) }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('school.transport.fee-schedules.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $sched->id }}">
                                    <button type="submit" class="btn-action-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 24px;">No Transport Fee Schedules created for this session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD/EDIT TRANSPORT SCHEDULE
     ============================================== -->
<div class="modal-overlay" id="scheduleModal" onclick="closeModalOnOutsideClick(event, 'scheduleModal')">
    <div class="modal-content-custom">
        <div class="modal-hdr">
            <h3 id="modalTitle">Create Transport Fee Schedule</h3>
            <button class="modal-close" onclick="closeModal('scheduleModal')">&times;</button>
        </div>
        <form action="{{ route('school.transport.fee-schedules') }}" method="POST" id="scheduleForm">
            @csrf
            <input type="hidden" name="action" id="formAction" value="add_transport_schedule">
            <input type="hidden" name="id" id="scheduleId" value="">
            <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
            
            <div class="modal-body-custom">
                <div class="form-group">
                    <label class="form-label">Schedule Name *</label>
                    <input type="text" name="name" id="scheduleName" class="form-control" placeholder="e.g. Transport Schedule 2026-27" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Route Mapping *</label>
                    <select name="route_id" id="routeId" class="form-control" onchange="updateRefRate(this)">
                        <option value="">School-wide Default (Fallback)</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" data-fare="{{ $route->total_fare }}">{{ $route->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="refRateWrapper" style="display:none; background:#eff6ff; padding:10px; border-radius:6px; border: 1px solid #bfdbfe;">
                    <span style="font-size:12px; font-weight:700; color:#1e40af;">Reference monthly rate: <span id="refRateVal">₹0.00</span></span>
                </div>

                <div class="form-group">
                    @include('school.fees.partials.installment_builder')
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" id="isActive" value="1" checked style="width:16px; height:16px;">
                    <label for="isActive" style="margin:0; font-weight:600; cursor:pointer;">Active</label>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn btn-outline" onclick="closeModal('scheduleModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Save Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function closeModalOnOutsideClick(e, id) {
        if (e.target === document.getElementById(id)) {
            closeModal(id);
        }
    }

    function updateRefRate(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const fare = option.getAttribute('data-fare');
        const wrapper = document.getElementById('refRateWrapper');
        const valSpan = document.getElementById('refRateVal');
        
        if (fare) {
            valSpan.innerText = '₹' + parseFloat(fare).toFixed(2);
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Create Transport Transport Schedule';
        document.getElementById('formAction').value = 'add_transport_schedule';
        document.getElementById('scheduleId').value = '';
        document.getElementById('scheduleName').value = '';
        document.getElementById('routeId').selectedIndex = 0;
        document.getElementById('refRateWrapper').style.display = 'none';
        document.getElementById('isActive').checked = true;

        if (window.loadInstallmentBuilderData) {
            window.loadInstallmentBuilderData({
                installment_type: 'custom',
                installments: [],
                fine_id: ''
            });
            document.getElementById('ibResetBtn').click();
        }

        openModal('scheduleModal');
    }

    function openEditModal(schedule) {
        document.getElementById('modalTitle').innerText = 'Edit Transport Fee Schedule';
        document.getElementById('formAction').value = 'edit_transport_schedule';
        document.getElementById('scheduleId').value = schedule.id;
        document.getElementById('scheduleName').value = schedule.name;
        document.getElementById('routeId').value = schedule.route_id || '';
        updateRefRate(document.getElementById('routeId'));
        document.getElementById('isActive').checked = !!schedule.is_active;

        if (window.loadInstallmentBuilderData) {
            window.loadInstallmentBuilderData(schedule);
        }

        openModal('scheduleModal');
    }
</script>
@endsection
