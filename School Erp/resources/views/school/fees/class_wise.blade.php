@extends('layouts.app')

@section('page-title', 'Class-wise Fee')

@section('content')
<style>
    /* Design Overrides for Premium Blue-and-White Theme */
    :root {
        --primary-blue: #1e3a8a;
        --sky-blue: #0284c7;
        --light-blue: #f0f9ff;
        --border-blue: #bae6fd;
        --text-dark: #0f172a;
        --white: #ffffff;
    }

    .class-wise-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-dark);
        font-size: 16px; /* Bigger font */
    }

    .class-wise-container h1, 
    .class-wise-container h2, 
    .class-wise-container h3, 
    .class-wise-container h4 {
        color: var(--primary-blue);
        font-weight: 700;
    }

    .class-wise-container .form-control {
        font-size: 16px; /* Bigger font */
        padding: 10px 14px;
        border: 1px solid var(--border-blue);
        border-radius: 8px;
        color: var(--text-dark);
        background-color: var(--white);
        height: auto;
    }
    
    .class-wise-container .form-label {
        font-size: 15px;
        font-weight: 600;
        color: var(--primary-blue);
        margin-bottom: 6px;
        display: block;
    }

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid var(--border-blue);
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
    }

    /* Schedule Cards */
    .schedule-cards-wrapper {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .schedule-card {
        flex: 1;
        min-width: 280px;
        background: #ffffff;
        border: 2px solid var(--border-blue);
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow);
    }

    .schedule-card.active {
        background: var(--primary-blue);
        border-color: var(--primary-blue);
        color: #ffffff;
    }

    .schedule-card.active h4, 
    .schedule-card.active p, 
    .schedule-card.active .schedule-amt {
        color: #ffffff !important;
    }

    .schedule-card:hover:not(.active) {
        border-color: var(--sky-blue);
        background: var(--light-blue);
    }

    .schedule-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: var(--light-blue);
        color: var(--sky-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .schedule-card.active .schedule-icon-box {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .schedule-info {
        flex: 1;
    }

    .schedule-amt {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary-blue);
    }

    .schedule-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--t2);
        text-transform: capitalize;
    }

    /* Logs Button */
    .logs-btn {
        background: var(--white);
        border: 2px solid var(--primary-blue);
        color: var(--primary-blue);
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .logs-btn:hover {
        background: var(--light-blue);
        color: var(--sky-blue);
        border-color: var(--sky-blue);
    }

    /* Category Blocks */
    .category-section {
        background: #ffffff;
        border: 1px solid var(--border-blue);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow);
    }

    .category-title-bar {
        border-bottom: 2px solid var(--light-blue);
        padding-bottom: 12px;
        margin-bottom: 16px;
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-blue);
    }

    /* Table styles */
    .fee-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }

    .fee-table th {
        background: var(--primary-blue);
        color: #ffffff;
        text-align: left;
        padding: 14px 16px;
        font-size: 16px;
        font-weight: 700;
    }

    .fee-table th:first-child {
        border-top-left-radius: 8px;
    }

    .fee-table th:last-child {
        border-top-right-radius: 8px;
    }

    .fee-row {
        border-bottom: 1px solid var(--border-blue);
        transition: background 0.2s ease;
    }

    .fee-row:hover {
        background: var(--light-blue);
    }

    .fee-row td {
        padding: 16px;
        font-size: 16px;
        color: var(--text-dark);
        vertical-align: middle;
    }

    /* Form Switch Toggle */
    .switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 26px;
    }

    .switch input {
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
        transition: .4s;
        border-radius: 26px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--sky-blue);
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    /* Accordion Details */
    .details-row {
        background: #f8fafc;
    }

    .details-container {
        padding: 24px;
        border-left: 4px solid var(--sky-blue);
        background: var(--white);
        margin: 10px 16px 20px;
        border-radius: 8px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }

    .installment-grid {
        display: grid;
        grid-template-columns: 1.5fr 2fr 1.5fr;
        gap: 16px;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 16px;
    }

    .installment-grid-hdr {
        font-weight: 700;
        color: var(--primary-blue);
        border-bottom: 2px solid var(--light-blue);
        padding-bottom: 10px;
    }

    .inst-amount-input {
        max-width: 160px;
        font-weight: 700;
        color: var(--primary-blue);
        text-align: right;
        font-size: 16px;
    }

    .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--primary-blue);
        font-size: 18px;
        padding: 6px 10px;
        transition: color 0.2s ease;
    }

    .action-btn:hover {
        color: var(--sky-blue);
    }

    .save-row-btn {
        background: var(--primary-blue);
        color: #ffffff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .save-row-btn:hover {
        background: var(--sky-blue);
    }

    .save-btn-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    /* Custom Toast Notification */
    .toast-notification {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: var(--primary-blue);
        color: #ffffff;
        padding: 16px 28px;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        display: none;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 16px;
        border-left: 5px solid #10b981;
    }

    /* Logs Modal */
    .logs-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .logs-modal-content {
        background: #ffffff;
        border-radius: 16px;
        width: 90%;
        max-width: 750px;
        max-height: 85vh;
        overflow-y: auto;
        padding: 28px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-blue);
    }

    .logs-modal-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid var(--light-blue);
        padding-bottom: 16px;
        margin-bottom: 20px;
    }

    .logs-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--t2);
        cursor: pointer;
    }

    .logs-close-btn:hover {
        color: var(--red);
    }

    .log-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 15px;
        line-height: 1.5;
    }

    .log-item:hover {
        background: var(--light-blue);
    }

    /* ── CLASS-WISE FEE DARK MODE OVERRIDES ── */
    body.dark-mode {
        --primary-blue: #818cf8;
        --sky-blue: #38bdf8;
        --light-blue: rgba(56, 189, 248, 0.15);
        --border-blue: #1e293b;
        --text-dark: #f8fafc;
        --white: #111827;
        --t2: #94a3b8;
    }
    body.dark-mode .class-wise-container {
        color: #f8fafc !important;
    }
    body.dark-mode .filter-card,
    body.dark-mode .schedule-card,
    body.dark-mode .category-section,
    body.dark-mode .logs-modal-content,
    body.dark-mode .details-container {
        background: #111827 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .details-row,
    body.dark-mode .schedule-icon-box {
        background: #1f2937 !important;
    }
    body.dark-mode .installment-grid,
    body.dark-mode .log-item {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .page-hdr-left h1 {
        color: #818cf8 !important;
    }
    body.dark-mode .page-hdr-left p {
        color: #94a3b8 !important;
    }
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
        border-color: #374151 !important;
    }
    body.dark-mode .form-control:focus {
        border-color: #38bdf8 !important;
    }
    body.dark-mode .schedule-card:hover:not(.active) {
        background: rgba(255, 255, 255, 0.03) !important;
        border-color: #38bdf8 !important;
    }
    body.dark-mode .fee-row:hover {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    body.dark-mode .log-item:hover {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    body.dark-mode .logs-btn {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .logs-btn:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
</style>

<div class="class-wise-container">
    <!-- Header -->
    <div class="page-hdr" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div class="page-hdr-left">
            <h1 style="font-size:28px; color:var(--primary-blue);"><i class="fas fa-school" style="margin-right:8px;"></i>Class-wise Fee</h1>
            <p style="font-size:16px; color:var(--t2); margin-top: 4px;">Configure installment schedules and amounts for Day boarding students</p>
        </div>
        <button class="logs-btn" onclick="showLogsModal()">
            <i class="fas fa-history"></i> Show Logs
        </button>
    </div>

    <!-- Filters Row -->
    <div class="filter-card">
        <form method="GET" action="{{ route('school.fees.class-wise') }}" id="filterForm">
            <div class="grid-3" style="gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Academic Year *</label>
                    <select name="academic_session_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $selectedSession->id == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Class *</label>
                    <select name="class_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass && $selectedClass->id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Section *</label>
                    <select name="section_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $selectedSection && $selectedSection->id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Schedule Cards Row -->
    <div class="schedule-cards-wrapper">
        @foreach($schedules as $sched)
            @php
                // Get unique active configs to avoid counting duplicates in database if any (only Day boarding shown in UI)
                $uniqueActiveFees = $classWiseFees->where('fee_schedule_id', $sched->id)
                    ->where('is_active', true)
                    ->whereIn('student_category_id', $studentCategories->pluck('id'))
                    ->unique(function($item) {
                        return $item->student_category_id . '-' . $item->fee_component_id . '-' . $item->class_id . '-' . $item->section_id;
                    });
                $schedAmt = $uniqueActiveFees->sum('amount');
            @endphp
            <div class="schedule-card {{ $loop->first ? 'active' : '' }}" id="card-{{ $sched->id }}" onclick="switchSchedule({{ $sched->id }})">
                <div class="schedule-icon-box">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="schedule-info">
                    <div class="schedule-amt" id="card-amt-{{ $sched->id }}">₹ {{ number_format($schedAmt, 0) }}</div>
                    <div class="schedule-title">{{ $sched->name }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Schedule Details Tables -->
    @foreach($schedules as $sched)
        <div class="schedule-details-section" id="schedule-details-{{ $sched->id }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
            
            <div style="background: var(--primary-blue); color: #ffffff; padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; font-weight: 700; font-size: 18px; text-transform: capitalize; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> {{ $sched->name }}</span>
                <span style="font-size: 14px; font-weight: 500; opacity: 0.9;">Total Installments: {{ $sched->no_of_installments }}</span>
            </div>

            @foreach($studentCategories as $cat)
                <div class="category-section">
                    <div class="category-title-bar">
                        <i class="fas fa-user-tag" style="margin-right:8px; color:var(--sky-blue);"></i>{{ $cat->name }}
                    </div>
                    
                    <table class="fee-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Fee Type</th>
                                <th style="width: 15%; text-align: center;">Status</th>
                                <th style="width: 15%; text-align: center;">Installment</th>
                                <th style="width: 15%; text-align: right;">Amount</th>
                                <th style="width: 15%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($components as $comp)
                                @php
                                    // Find existing configuration
                                    $existingConfig = $classWiseFees->where('fee_schedule_id', $sched->id)
                                        ->where('student_category_id', $cat->id)
                                        ->where('fee_component_id', $comp->id)
                                        ->first();
                                    
                                    $isActive = $existingConfig ? $existingConfig->is_active : false;
                                    $totalAmount = $existingConfig ? $existingConfig->amount : 0.00;
                                    $savedInstallments = $existingConfig ? $existingConfig->installments : [];
                                    
                                    // Generate dates for this schedule
                                    $dates = \App\Http\Controllers\School\FeeManagementController::generateInstallmentDates($sched->start_date, $sched->end_date, $sched->no_of_installments);
                                @endphp
                                <!-- Row -->
                                <tr class="fee-row" id="row-{{ $sched->id }}-{{ $cat->id }}-{{ $comp->id }}">
                                    <td style="font-weight: 600;">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}. {{ $comp->component_name }}
                                    </td>
                                    <td style="text-align: center;">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-status" 
                                                   onchange="toggleRowActive({{ $sched->id }}, {{ $cat->id }}, {{ $comp->id }}, this.checked)"
                                                   {{ $isActive ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </td>
                                    <td style="text-align: center; font-weight: 600;">
                                        {{ $sched->no_of_installments }}
                                    </td>
                                    <td style="text-align: right; font-weight: 800; color: var(--primary-blue);" id="display-amt-{{ $sched->id }}-{{ $cat->id }}-{{ $comp->id }}">
                                        {{ $isActive && $totalAmount > 0 ? '₹ ' . number_format($totalAmount, 0) : '-' }}
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="action-btn" onclick="toggleAccordion({{ $sched->id }}, {{ $cat->id }}, {{ $comp->id }})" title="Expand/Collapse">
                                            <i class="fas fa-chevron-down" id="arrow-{{ $sched->id }}-{{ $cat->id }}-{{ $comp->id }}"></i>
                                        </button>
                                        <button class="action-btn" onclick="copyInstallments({{ $sched->id }}, {{ $cat->id }}, {{ $comp->id }}, '{{ $comp->component_name }}')" title="Copy to other categories">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Accordion details row -->
                                <tr class="details-row" id="accordion-{{ $sched->id }}-{{ $cat->id }}-{{ $comp->id }}" style="display: none;">
                                    <td colspan="5" style="padding: 0;">
                                        <div class="details-container">
                                            <div class="installment-grid installment-grid-hdr">
                                                <div>Installments</div>
                                                <div>Installments Date</div>
                                                <div style="text-align: right; padding-right: 16px;">Installment amount</div>
                                            </div>
                                            
                                            <form class="installment-form" id="form-{{ $sched->id }}-{{ $cat->id }}-{{ $comp->id }}">
                                                @for($i = 0; $i < $sched->no_of_installments; $i++)
                                                    @php
                                                        // Find saved amount for this installment, or default to 0
                                                        $savedAmt = 0;
                                                        $dateRange = $dates[$i] ?? '';
                                                        
                                                        if (!empty($savedInstallments)) {
                                                            foreach($savedInstallments as $si) {
                                                                if (($si['installment_no'] ?? null) == ($i + 1)) {
                                                                    $savedAmt = $si['amount'] ?? 0;
                                                                    if (!empty($si['date_range'])) {
                                                                        $dateRange = $si['date_range'];
                                                                    }
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="installment-grid">
                                                        <div style="font-weight: 600; color: var(--t2);">installment {{ $i + 1 }}</div>
                                                        <div style="color: var(--sky-blue); font-weight: 700;">
                                                            <input type="hidden" name="installments[{{ $i }}][installment_no]" value="{{ $i + 1 }}">
                                                            <input type="hidden" name="installments[{{ $i }}][date_range]" value="{{ $dateRange }}">
                                                            {{ $dateRange }}
                                                        </div>
                                                        <div style="text-align: right; display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
                                                            <span style="font-weight: 700; color: var(--primary-blue);">₹</span>
                                                            <input type="number" name="installments[{{ $i }}][amount]" 
                                                                   class="form-control inst-amount-input inst-val-{{ $sched->id }}-{{ $cat->id }}-{{ $comp->id }}" 
                                                                   value="{{ $savedAmt }}" min="0" placeholder="0" required
                                                                   oninput="calculateLiveTotal({{ $sched->id }}, {{ $cat->id }}, {{ $comp->id }})">
                                                        </div>
                                                    </div>
                                                @endfor
                                                
                                                <div class="save-btn-container">
                                                    <button type="button" class="save-row-btn" onclick="saveComponentFee({{ $sched->id }}, {{ $cat->id }}, {{ $comp->id }})">
                                                        <i class="fas fa-save"></i> Save
                                                     </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @if($selectedClass)
                    <div style="margin-top: 16px; text-align: right;">
                        <a href="{{ route('school.fees.student-wise') }}?class_id={{ $selectedClass->id }}&section_id={{ $selectedSection ? $selectedSection->id : '' }}"
                           style="color: var(--sky-blue); font-weight: 700; font-size: 15px; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                            STUDENT FEE DETAILS FOR CLASS {{ $selectedClass->name }} {{ $selectedSection ? $selectedSection->name : '' }} <i class="fas fa-chevron-right" style="margin-left:4px;"></i>
                        </a>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>

<!-- Logs Modal Container -->
<div class="logs-modal" id="logsModal">
    <div class="logs-modal-content">
        <div class="logs-modal-hdr">
            <h3 style="font-size:22px; color:var(--primary-blue); display:flex; align-items:center; gap:10px;">
                <i class="fas fa-history"></i> Class-wise Fee Setup Logs
            </h3>
            <button class="logs-close-btn" onclick="hideLogsModal()">&times;</button>
        </div>
        <div id="logsContent">
            <!-- Dynamic logs loaded dynamically via AJAX or preloaded -->
            @php
                $auditLogs = \App\Models\ClassWiseFee::where('school_id', auth()->user()->school_id)
                    ->with(['class', 'section', 'feeSchedule', 'studentCategory', 'feeComponent'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp
            @forelse($auditLogs as $log)
                <div class="log-item">
                    <div style="display:flex; justify-content:space-between; margin-bottom: 4px;">
                        <strong style="color:var(--primary-blue);">{{ optional($log->feeComponent)->component_name }}</strong>
                        <span style="font-size:13px; color:var(--t2); font-family: monospace;">{{ $log->updated_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div style="color:var(--t1);">
                        Allocated to <strong>{{ $log->class->name }} {{ optional($log->section)->name ?? 'All Sections' }}</strong> ({{ $log->studentCategory->name }}) under <strong>{{ $log->feeSchedule->name }}</strong>. 
                        Status: <span class="badge {{ $log->is_active ? 'badge-success' : 'badge-danger' }}">{{ $log->is_active ? 'Active' : 'Inactive' }}</span> | Amount: <strong>₹{{ number_format($log->amount, 2) }}</strong>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding: 40px; color:var(--t3);">
                    <i class="fas fa-info-circle" style="font-size:32px; margin-bottom:12px; display:block;"></i>
                    No class-wise fee modifications logged yet.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Custom Toast -->
<div class="toast-notification" id="toastNotification">
    <i class="fas fa-check-circle" style="color:#10b981; font-size:20px;"></i>
    <span id="toastMessage">Configuration saved successfully!</span>
</div>

<script>
    // Switch between schedules on click
    function switchSchedule(scheduleId) {
        document.querySelectorAll('.schedule-details-section').forEach(sec => {
            sec.style.display = 'none';
        });
        document.querySelectorAll('.schedule-card').forEach(card => {
            card.classList.remove('active');
        });
        
        document.getElementById(`schedule-details-${scheduleId}`).style.display = 'block';
        document.getElementById(`card-${scheduleId}`).classList.add('active');
    }

    // Toggle Accordion Collapse
    function toggleAccordion(scheduleId, categoryId, componentId) {
        const accordionRow = document.getElementById(`accordion-${scheduleId}-${categoryId}-${componentId}`);
        const arrowIcon = document.getElementById(`arrow-${scheduleId}-${categoryId}-${componentId}`);
        
        if (accordionRow.style.display === 'none') {
            accordionRow.style.display = 'table-row';
            arrowIcon.className = 'fas fa-chevron-up';
        } else {
            accordionRow.style.display = 'none';
            arrowIcon.className = 'fas fa-chevron-down';
        }
    }

    // Live calculation of amount inside the accordion as inputs change
    function calculateLiveTotal(scheduleId, categoryId, componentId) {
        let total = 0;
        document.querySelectorAll(`.inst-val-${scheduleId}-${categoryId}-${componentId}`).forEach(input => {
            total += parseFloat(input.value || 0);
        });
        
        const row = document.getElementById(`row-${scheduleId}-${categoryId}-${componentId}`);
        const toggle = row.querySelector('.toggle-status');
        const displayAmt = document.getElementById(`display-amt-${scheduleId}-${categoryId}-${componentId}`);
        
        if (toggle.checked && total > 0) {
            displayAmt.innerText = '₹ ' + total.toLocaleString('en-IN');
        } else if (toggle.checked) {
            displayAmt.innerText = '₹ 0';
        } else {
            displayAmt.innerText = '-';
        }
    }

    // Auto-save toggle status change
    function toggleRowActive(scheduleId, categoryId, componentId, isChecked) {
        const row = document.getElementById(`row-${scheduleId}-${categoryId}-${componentId}`);
        const toggle = row.querySelector('.toggle-status');

        // If toggling ON, check that at least one installment has a non-zero amount
        if (isChecked) {
            let hasAmount = false;
            document.querySelectorAll(`.inst-val-${scheduleId}-${categoryId}-${componentId}`).forEach(input => {
                if (parseFloat(input.value || 0) > 0) hasAmount = true;
            });
            if (!hasAmount) {
                // Open accordion so user can fill amounts
                const accordion = document.getElementById(`accordion-${scheduleId}-${categoryId}-${componentId}`);
                if (accordion && accordion.style.display === 'none') {
                    toggleAccordion(scheduleId, categoryId, componentId);
                }
                showToast('⚠️ Please enter installment amounts before enabling this fee.', true);
                // Revert toggle back to OFF without saving
                toggle.checked = false;
                return;
            }
        }

        const form = document.getElementById(`form-${scheduleId}-${categoryId}-${componentId}`);
        const formData = new FormData(form);
        
        formData.append('academic_session_id', '{{ $selectedSession->id }}');
        formData.append('class_id', '{{ $selectedClass ? $selectedClass->id : "" }}');
        formData.append('section_id', '{{ $selectedSection ? $selectedSection->id : "" }}');
        formData.append('fee_schedule_id', scheduleId);
        formData.append('student_category_id', categoryId);
        formData.append('fee_component_id', componentId);
        formData.append('is_active', isChecked ? 'true' : 'false');
        
        // Calculate total amount
        let total = 0;
        document.querySelectorAll(`.inst-val-${scheduleId}-${categoryId}-${componentId}`).forEach(input => {
            total += parseFloat(input.value || 0);
        });
        formData.append('amount', total);

        fetch('{{ route("school.fees.class-wise") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Keep the toggle in the correct state
                toggle.checked = isChecked;

                const displayAmt = document.getElementById(`display-amt-${scheduleId}-${categoryId}-${componentId}`);
                if (isChecked && total > 0) {
                    displayAmt.innerText = '₹ ' + total.toLocaleString('en-IN');
                } else if (isChecked) {
                    displayAmt.innerText = '₹ 0';
                } else {
                    displayAmt.innerText = '-';
                }
                
                updateCardTotal(scheduleId);
                showToast(isChecked ? 'Fee Component activated!' : 'Fee Component deactivated!');
                reloadLogs();
            } else {
                // On failure: revert toggle to previous state
                toggle.checked = !isChecked;
                showToast('Failed to update status', true);
            }
        })
        .catch(err => {
            console.error(err);
            // On error: revert toggle to previous state
            toggle.checked = !isChecked;
            showToast('Connection error. Please try again.', true);
        });
    }

    // Save individual component installments & amount
    function saveComponentFee(scheduleId, categoryId, componentId) {
        const row = document.getElementById(`row-${scheduleId}-${categoryId}-${componentId}`);
        const toggle = row.querySelector('.toggle-status');
        const isChecked = toggle.checked;

        const form = document.getElementById(`form-${scheduleId}-${categoryId}-${componentId}`);
        const formData = new FormData(form);
        
        formData.append('academic_session_id', '{{ $selectedSession->id }}');
        formData.append('class_id', '{{ $selectedClass ? $selectedClass->id : "" }}');
        formData.append('section_id', '{{ $selectedSection ? $selectedSection->id : "" }}');
        formData.append('fee_schedule_id', scheduleId);
        formData.append('student_category_id', categoryId);
        formData.append('fee_component_id', componentId);
        formData.append('is_active', isChecked ? 'true' : 'false');
        
        // Calculate total amount
        let total = 0;
        document.querySelectorAll(`.inst-val-${scheduleId}-${categoryId}-${componentId}`).forEach(input => {
            total += parseFloat(input.value || 0);
        });
        formData.append('amount', total);

        fetch('{{ route("school.fees.class-wise") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const displayAmt = document.getElementById(`display-amt-${scheduleId}-${categoryId}-${componentId}`);
                if (isChecked && total > 0) {
                    displayAmt.innerText = '₹ ' + total.toLocaleString('en-IN');
                } else if (isChecked) {
                    displayAmt.innerText = '₹ 0';
                } else {
                    displayAmt.innerText = '-';
                }
                
                updateCardTotal(scheduleId);
                showToast('Configuration saved successfully!');
                toggleAccordion(scheduleId, categoryId, componentId);
                reloadLogs();
            } else {
                showToast('Failed to save configuration', true);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Connection error. Please try again.', true);
        });
    }

    // Dynamic schedule card total calculator
    function updateCardTotal(scheduleId) {
        let schedTotal = 0;
        
        const rows = document.querySelectorAll(`[id^="row-${scheduleId}-"]`);
        rows.forEach(row => {
            const toggle = row.querySelector('.toggle-status');
            if (toggle && toggle.checked) {
                const idParts = row.id.split('-');
                const catId = idParts[2];
                const compId = idParts[3];
                
                document.querySelectorAll(`.inst-val-${scheduleId}-${catId}-${compId}`).forEach(input => {
                    schedTotal += parseFloat(input.value || 0);
                });
            }
        });
        
        const cardAmt = document.getElementById(`card-amt-${scheduleId}`);
        if (cardAmt) {
            cardAmt.innerText = '₹ ' + schedTotal.toLocaleString('en-IN');
        }
    }

    // Copy installments configuration from one category to others
    function copyInstallments(scheduleId, fromCategoryId, componentId, componentName) {
        const sourceInputs = document.querySelectorAll(`.inst-val-${scheduleId}-${fromCategoryId}-${componentId}`);
        const sourceToggle = document.querySelector(`#row-${scheduleId}-${fromCategoryId}-${componentId} .toggle-status`).checked;
        
        const categories = [
            @foreach($studentCategories as $cat)
                { id: {{ $cat->id }}, name: "{{ $cat->name }}" },
            @endforeach
        ];
        
        categories.forEach(cat => {
            if (cat.id !== fromCategoryId) {
                const targetInputs = document.querySelectorAll(`.inst-val-${scheduleId}-${cat.id}-${componentId}`);
                for (let i = 0; i < sourceInputs.length; i++) {
                    if (targetInputs[i] && sourceInputs[i]) {
                        targetInputs[i].value = sourceInputs[i].value;
                    }
                }
                
                const targetToggle = document.querySelector(`#row-${scheduleId}-${cat.id}-${componentId} .toggle-status`);
                if (targetToggle) {
                    targetToggle.checked = sourceToggle;
                }
                
                // Immediately save the updated target row
                toggleRowActive(scheduleId, cat.id, componentId, sourceToggle);
            }
        });
        
        showToast(`Copied ${componentName} configuration to other categories!`);
    }

    // Toast alerts helper
    function showToast(message, isError = false) {
        const toast = document.getElementById('toastNotification');
        const msgSpan = document.getElementById('toastMessage');
        
        msgSpan.innerText = message;
        if (isError) {
            toast.style.borderLeftColor = '#ef4444';
            toast.querySelector('i').className = 'fas fa-exclamation-circle';
            toast.querySelector('i').style.color = '#ef4444';
        } else {
            toast.style.borderLeftColor = '#10b981';
            toast.querySelector('i').className = 'fas fa-check-circle';
            toast.querySelector('i').style.color = '#10b981';
        }
        
        toast.style.display = 'flex';
        
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3500);
    }

    // Logs Modals Helpers
    function showLogsModal() {
        document.getElementById('logsModal').style.display = 'flex';
    }

    function hideLogsModal() {
        document.getElementById('logsModal').style.display = 'none';
    }

    // Reload log entries asynchronously
    function reloadLogs() {
        // Simple fetch of the current page HTML, parse the logs table, and replace the content
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newLogs = doc.getElementById('logsContent').innerHTML;
                document.getElementById('logsContent').innerHTML = newLogs;
            });
    }

    // Close logs modal if clicked outside
    window.onclick = function(event) {
        const modal = document.getElementById('logsModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection
