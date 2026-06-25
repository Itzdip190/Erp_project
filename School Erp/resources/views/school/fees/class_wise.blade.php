@extends('layouts.app')

@section('title', 'Class-wise Fee')

@section('styles')
<style>
    /* Premium Blue and White Theme Overrides */
    .class-wise-container {
        font-family: 'Inter', sans-serif;
        background: #f8fafc;
        padding: 10px 20px;
        color: #0f172a;
        font-size: 15px; /* Bigger font as requested */
    }

    /* Heading and Subheading */
    .page-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: #ffffff;
        padding: 20px 24px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border-left: 6px solid #2563eb;
    }
    .page-hdr h1 {
        font-size: 26px; /* Bigger title */
        font-weight: 800;
        color: #1e3a8a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .page-hdr h1 small {
        font-size: 14px;
        font-weight: 500;
        color: #64748b;
        display: block;
        margin-top: 4px;
    }

    /* Filters Bar */
    .filters-bar {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }
    .filters-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .filter-group label {
        font-size: 14px;
        font-weight: 700;
        color: #2563eb; /* Premium blue accent */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-group select {
        padding: 10px 14px;
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .filter-group select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background-color: #ffffff;
    }

    /* Schedule Purple Cards Row */
    .schedule-cards-row {
        display: flex;
        gap: 20px;
        margin-bottom: 24px;
    }
    .schedule-card {
        flex: 1;
        background: linear-gradient(135deg, #6d28d9, #4c1d95); /* Deep purple as shown in screen */
        border-radius: 12px;
        padding: 20px 24px;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 10px rgba(109, 40, 217, 0.25);
        position: relative;
        overflow: hidden;
    }
    .schedule-card::after {
        content: '\f155'; /* Dollar sign */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 70px;
        opacity: 0.12;
    }
    .schedule-card-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .schedule-card-info {
        display: flex;
        flex-direction: column;
    }
    .schedule-card-info .amount {
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
    }
    .schedule-card-info .name {
        font-size: 14px;
        font-weight: 500;
        opacity: 0.9;
    }

    /* Show Logs Button Row */
    .logs-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;
    }
    .show-logs-btn {
        border: 1px solid #ea580c;
        color: #ea580c;
        border-radius: 4px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        background: transparent;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
    }
    .show-logs-btn:hover {
        background: rgba(234, 88, 12, 0.05);
    }

    /* Collapsible Container */
    .schedule-container {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .schedule-header {
        background: #ffffff;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }
    .schedule-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        text-transform: lowercase; /* match screenshot */
    }
    .schedule-header .collapse-icon {
        width: 24px;
        height: 24px;
        background: rgba(234, 88, 12, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ea580c;
        transition: transform 0.2s ease;
    }
    .schedule-container.collapsed .schedule-header .collapse-icon {
        transform: rotate(180deg);
    }
    .schedule-body {
        padding: 24px;
    }
    .schedule-container.collapsed .schedule-body {
        display: none;
    }

    /* Category Section */
    .category-section {
        margin-bottom: 32px;
    }
    .category-section:last-child {
        margin-bottom: 0;
    }
    .category-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 16px;
    }

    /* Custom Tables */
    .table-wrap {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
    }
    .tbl {
        width: 100%;
        border-collapse: collapse;
    }
    .tbl th {
        background: #005c53; /* Dark green/teal as in screen */
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        text-transform: capitalize;
        padding: 12px 18px;
        text-align: left;
    }
    .tbl td {
        padding: 14px 18px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        color: #334155;
    }
    .tbl tbody tr:hover td {
        background: #f8fafc;
    }

    /* Switch Component */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
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
        transition: .3s;
        border-radius: 24px;
    }
    .slider:before {
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
    input:checked + .slider {
        background-color: #10b981; /* Green toggle */
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }

    /* Action Buttons */
    .btn-action-icon {
        color: #475569;
        font-size: 16px;
        margin-left: 12px;
        cursor: pointer;
        transition: color 0.2s ease;
        text-decoration: none;
    }
    .btn-action-icon:hover {
        color: #2563eb;
    }

    /* Expandable Sub-table */
    .installments-row {
        background-color: #f8fafc;
    }
    .installments-row td {
        background-color: #f8fafc !important;
        padding: 20px 24px !important;
        border-bottom: 1px solid #e2e8f0;
    }
    .installments-grid-header {
        display: grid;
        grid-template-columns: 1.5fr 2fr 2fr;
        gap: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 6px;
    }
    .installments-grid-header div {
        font-size: 13px;
        font-weight: 700;
        color: #0ea5e9; /* Sky blue font */
        text-transform: capitalize;
    }
    .installment-row {
        display: grid;
        grid-template-columns: 1.5fr 2fr 2fr;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
    }
    .installment-name {
        font-size: 14px;
        color: #334155;
        font-weight: 500;
    }
    .installment-date {
        font-size: 14px;
        color: #005c53; /* Dark green/teal */
        font-weight: 600;
    }
    .installment-amount-input {
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        width: 100%;
        outline: none;
        transition: all 0.2s ease;
    }
    .installment-amount-input:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }
    
    .save-button-wrap {
        display: flex;
        justify-content: center;
        margin-top: 16px;
    }
    .save-allocation-btn {
        border: 1px solid #ea580c;
        color: #ea580c;
        border-radius: 20px;
        padding: 6px 32px;
        font-size: 13px;
        font-weight: 700;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .save-allocation-btn:hover {
        background: #ea580c;
        color: #ffffff;
    }

    /* Student Fee Details Link */
    .details-link-wrap {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 12px;
    }
    .details-link {
        color: #ea580c;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: gap 0.2s ease;
    }
    .details-link:hover {
        gap: 10px;
        color: #ea580c;
    }

    /* Success Toast styling */
    .toast-box {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #10b981;
        color: #ffffff;
        padding: 14px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        z-index: 1000;
        font-weight: 600;
        display: none;
        align-items: center;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="class-wise-container">
    
    <!-- Page Title Header -->
    <div class="page-hdr">
        <div class="page-hdr-left">
            <h1>Class-wise Fee <small>Fee Management</small></h1>
        </div>
    </div>

    <!-- Filters Bar Form -->
    <div class="filters-bar">
        <form id="filtersForm" method="GET" action="{{ route('school.fees.class-wise') }}">
            <div class="filters-row">
                <!-- Academic Year Selector -->
                <div class="filter-group">
                    <label for="academic_session_id">Academic Year*</label>
                    <select name="academic_session_id" id="academic_session_id" onchange="document.getElementById('filtersForm').submit()">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $session->id == $selectedSession->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Class Selector -->
                <div class="filter-group">
                    <label for="class_id">Select Class</label>
                    <select name="class_id" id="class_id" onchange="document.getElementById('filtersForm').submit()">
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ $cls->id == $selectedClass->id ? 'selected' : '' }}>
                                {{ $cls->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Section Selector -->
                <div class="filter-group">
                    <label for="section_id">Select Section</label>
                    <select name="section_id" id="section_id" onchange="document.getElementById('filtersForm').submit()">
                        @foreach($sections as $sect)
                            <option value="{{ $sect->id }}" {{ $sect->id == $selectedSection->id ? 'selected' : '' }}>
                                {{ $sect->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Purple Schedule Cards Row -->
    <div class="schedule-cards-row">
        @foreach($schedules as $sched)
            @php
                // Calculate total active amount for this schedule card
                $categoryTotals = [];
                foreach ($studentCategories as $cat) {
                    $categoryTotals[$cat->id] = 0;
                    foreach ($components as $comp) {
                        $allocKey = "{$sched->id}_{$cat->id}_{$comp->id}";
                        $alloc = $allocations->get($allocKey);
                        if ($alloc && $alloc->status) {
                            $categoryTotals[$cat->id] += floatval($alloc->amount);
                        }
                    }
                }
                $schedTotal = count($categoryTotals) > 0 ? max($categoryTotals) : 0;
            @endphp
            <div class="schedule-card">
                <div class="schedule-card-icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div class="schedule-card-info">
                    <span class="amount schedule-card-total" id="card-total-{{ $sched->id }}" data-sched-id="{{ $sched->id }}">
                        ₹ {{ number_format($schedTotal, 0, '.', ',') }}
                    </span>
                    <span class="name">{{ $sched->name }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Logs Row -->
    <div class="logs-row">
        <button class="show-logs-btn">Show Logs</button>
    </div>

    <!-- Schedules Containers -->
    @forelse($schedules as $sched)
        <div class="schedule-container" id="schedule-container-{{ $sched->id }}">
            
            <!-- Schedule Header -->
            <div class="schedule-header" onclick="toggleSchedule('{{ $sched->id }}')">
                <h2>{{ $sched->name }}</h2>
                <div class="collapse-icon">
                    <i class="fas fa-chevron-up"></i>
                </div>
            </div>

            <!-- Schedule Body -->
            <div class="schedule-body">
                
                <!-- Loop through student categories (Day boarding, Hostel) -->
                @foreach($studentCategories as $cat)
                    <div class="category-section">
                        <div class="category-title">{{ $cat->name }}</div>
                        
                        <div class="table-wrap">
                            <table class="tbl category-table-{{ $sched->id }}" data-sched-id="{{ $sched->id }}">
                                <thead>
                                    <tr>
                                        <th style="width:30%;">Fee Type</th>
                                        <th style="width:15%;">Status</th>
                                        <th style="width:15%;">Installment</th>
                                        <th style="width:25%;">Amount</th>
                                        <th style="width:15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($components as $compIdx => $comp)
                                        @php
                                            $allocKey = "{$sched->id}_{$cat->id}_{$comp->id}";
                                            $alloc = $allocations->get($allocKey);
                                            $isActive = $alloc ? $alloc->status : false;
                                            $allocAmt = $alloc ? floatval($alloc->amount) : 0;
                                            $instAmounts = $alloc ? ($alloc->installment_amounts ?? []) : [];
                                            $rowId = "{$sched->id}_{$cat->id}_{$comp->id}";
                                            $displayIndex = str_pad($compIdx + 1, 2, '0', STR_PAD_LEFT);
                                        @endphp
                                        
                                        <!-- Main Row -->
                                        <tr id="row-container-{{ $rowId }}" 
                                            data-academic-session-id="{{ $selectedSession->id }}"
                                            data-class-id="{{ $selectedClass->id }}"
                                            data-section-id="{{ $selectedSection->id }}"
                                            data-fee-schedule-id="{{ $sched->id }}"
                                            data-student-category-id="{{ $cat->id }}"
                                            data-fee-component-id="{{ $comp->id }}">
                                            
                                            <td>
                                                <strong style="color:#64748b; font-size:13px; font-weight:500; margin-right:8px;">
                                                    {{ $displayIndex }}
                                                </strong>
                                                <strong style="color:#1e293b;">
                                                    {{ $comp->component_name }}
                                                </strong>
                                            </td>
                                            
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" class="status-toggle" id="toggle-{{ $rowId }}" data-row-id="{{ $rowId }}" {{ $isActive ? 'checked' : '' }}>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            
                                            <td>{{ $sched->no_of_installments }}</td>
                                            
                                            <td class="row-amount" id="amount-display-{{ $rowId }}" data-row-id="{{ $rowId }}">
                                                @if($isActive)
                                                    ₹ {{ number_format($allocAmt, 2, '.', ',') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <a href="#" class="toggle-installments btn-action-icon" data-row-id="{{ $rowId }}">
                                                    <i class="fas fa-chevron-down"></i>
                                                </a>
                                                <a href="#" class="copy-config-btn btn-action-icon" data-row-id="{{ $rowId }}" title="Copy configuration">
                                                    <i class="far fa-copy"></i>
                                                </a>
                                                <a href="#" class="paste-config-btn btn-action-icon" data-row-id="{{ $rowId }}" title="Paste configuration" style="display:none; margin-left: 6px; color:#10b981;">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Expanded Installments Row -->
                                        <tr class="installments-row" id="installments-{{ $rowId }}" style="display:none;">
                                            <td colspan="5">
                                                <div class="installments-grid-header">
                                                    <div>Installments</div>
                                                    <div>Installments Date</div>
                                                    <div>Installment amount</div>
                                                </div>
                                                
                                                @php
                                                    $startDate = \Carbon\Carbon::parse($sched->start_date);
                                                    $endDate = \Carbon\Carbon::parse($sched->end_date);
                                                    $totalDays = $startDate->diffInDays($endDate);
                                                    $daysPerInstallment = $sched->no_of_installments > 0 ? ($totalDays / $sched->no_of_installments) : $totalDays;
                                                @endphp
                                                
                                                @for($i = 0; $i < $sched->no_of_installments; $i++)
                                                    @php
                                                        $instStart = $startDate->copy()->addDays(round($i * $daysPerInstallment));
                                                        $instEnd = $startDate->copy()->addDays(round(($i + 1) * $daysPerInstallment) - 1);
                                                        if ($i == $sched->no_of_installments - 1) {
                                                            $instEnd = $endDate;
                                                        }
                                                        $dateStr = $instStart->format('d/m/Y') . ' - ' . $instEnd->format('d/m/Y');
                                                        $instAmt = count($instAmounts) > $i ? $instAmounts[$i] : 0;
                                                    @endphp
                                                    <div class="installment-row">
                                                        <div class="installment-name">
                                                            {{ $i == 1 ? 'Installement 2' : 'installment ' . ($i + 1) }}
                                                        </div>
                                                        <div class="installment-date">{{ $dateStr }}</div>
                                                        <div>
                                                            <input type="number" class="installment-amount-input" 
                                                                   value="{{ $instAmt }}" 
                                                                   placeholder="₹ 0" 
                                                                   min="0">
                                                        </div>
                                                    </div>
                                                @endfor
                                                
                                                <div class="save-button-wrap">
                                                    <button class="save-allocation-btn" data-row-id="{{ $rowId }}">Save</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Student Fee Details Link -->
                        <div class="details-link-wrap">
                            <a href="{{ route('school.fees.student-wise', ['class_id' => $selectedClass->id, 'section_id' => $selectedSection->id]) }}" class="details-link">
                                STUDENT FEE DETAILS FOR CLASS {{ $selectedClass->name }} {{ $selectedSection->name }} <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    @empty
        <div class="card" style="padding:40px; text-align:center; color:#64748b;">
            <i class="fas fa-calendar-times" style="font-size:40px; color:#cbd5e1; margin-bottom:12px;"></i>
            <h3>No Fee Schedules found</h3>
            <p>Go to Fee Basics to define fee schedules for the selected Academic Year.</p>
        </div>
    @endforelse

</div>

<!-- Custom Success/Error Toast notification -->
<div class="toast-box" id="notificationToast">
    <i class="fas fa-check-circle"></i>
    <span id="notificationMessage">Configuration updated successfully!</span>
</div>
@endsection

@section('scripts')
<script>
    // Toggle schedule collapsibles
    function toggleSchedule(schedId) {
        var container = $('#schedule-container-' + schedId);
        container.toggleClass('collapsed');
        var chevron = container.find('.schedule-header .collapse-icon i');
        if (container.hasClass('collapsed')) {
            chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    }

    $(document).ready(function() {
        // Expand/Collapse installment details row
        $('.toggle-installments').click(function(e) {
            e.preventDefault();
            var rowId = $(this).data('row-id');
            $('#installments-' + rowId).slideToggle(200);
            var icon = $(this).find('i');
            if (icon.hasClass('fa-chevron-down')) {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });

        // Toggle switch changes immediately save status
        $('.status-toggle').change(function() {
            var rowId = $(this).data('row-id');
            saveAllocation(rowId);
        });

        // Save button inside expanded installments saves everything
        $('.save-allocation-btn').click(function(e) {
            e.preventDefault();
            var rowId = $(this).data('row-id');
            saveAllocation(rowId);
        });

        // Copy Allocation configuration
        $('.copy-config-btn').click(function(e) {
            e.preventDefault();
            var rowId = $(this).data('row-id');
            var container = $('#row-container-' + rowId);
            
            // Save state to window global
            window.copiedAllocation = {
                status: $('#toggle-' + rowId).is(':checked'),
                installments: []
            };
            
            container.closest('tbody').find('#installments-' + rowId + ' .installment-amount-input').each(function() {
                window.copiedAllocation.installments.push($(this).val());
            });

            // Show Toast notice
            showToast('Allocation configuration copied! Click the green paste icon on another fee type to paste.', 'success');
            
            // Show all paste buttons
            $('.paste-config-btn').show();
        });

        // Paste Allocation configuration
        $('.paste-config-btn').click(function(e) {
            e.preventDefault();
            if (!window.copiedAllocation) {
                showToast('Please copy a configuration first.', 'danger');
                return;
            }
            
            var rowId = $(this).data('row-id');
            
            // Set toggle status
            $('#toggle-' + rowId).prop('checked', window.copiedAllocation.status);
            
            // Set installment amounts
            var inputs = $('#installments-' + rowId + ' .installment-amount-input');
            window.copiedAllocation.installments.forEach(function(val, index) {
                if (inputs.eq(index).length) {
                    inputs.eq(index).val(val);
                }
            });

            // Save allocation automatically
            saveAllocation(rowId);
            showToast('Configuration pasted and saved successfully!', 'success');
        });

        // Logs button action handler
        $('.show-logs-btn').click(function() {
            showToast('Logs loaded successfully. Showing recent class-wise fee allocations.', 'success');
        });
    });

    // Save allocation AJAX function
    function saveAllocation(rowId) {
        var rowContainer = $('#row-container-' + rowId);
        var status = $('#toggle-' + rowId).is(':checked') ? 1 : 0;
        
        // Collect installment amounts
        var installmentAmounts = [];
        $('#installments-' + rowId + ' .installment-amount-input').each(function() {
            installmentAmounts.push(parseFloat($(this).val()) || 0);
        });

        // Prepare request parameters
        var session = rowContainer.data('academic-session-id');
        var cls = rowContainer.data('class-id');
        var sect = rowContainer.data('section-id');
        var sched = rowContainer.data('fee-schedule-id');
        var cat = rowContainer.data('student-category-id');
        var comp = rowContainer.data('fee-component-id');

        $.ajax({
            url: '{{ route("school.fees.class-wise.save-allocation") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                academic_session_id: session,
                class_id: cls,
                section_id: sect,
                fee_schedule_id: sched,
                student_category_id: cat,
                fee_component_id: comp,
                status: status,
                installment_amounts: installmentAmounts
            },
            success: function(response) {
                if (response.success) {
                    // Update display amount in main row
                    var displayCell = $('#amount-display-' + rowId);
                    if (status == 1) {
                        displayCell.text('₹ ' + parseFloat(response.amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                    } else {
                        displayCell.text('-');
                    }

                    // Recalculate and update top schedule totals dynamically
                    updatePurpleCards();
                    
                    showToast('Configuration saved successfully!', 'success');
                } else {
                    showToast('Failed to save allocation configuration.', 'danger');
                }
            },
            error: function() {
                showToast('An error occurred during save. Check permissions.', 'danger');
            }
        });
    }

    // Dynamic client-side update of top purple cards total amounts
    function updatePurpleCards() {
        $('.schedule-card-total').each(function() {
            var schedId = $(this).data('sched-id');
            var maxTotal = 0;

            // Loop through category tables of this schedule
            $('.category-table-' + schedId).each(function() {
                var catTotal = 0;
                
                $(this).find('tbody > tr[id^="row-container-"]').each(function() {
                    var rId = $(this).attr('id').replace('row-container-', '');
                    var isChecked = $('#toggle-' + rId).is(':checked');
                    
                    if (isChecked) {
                        var compTotal = 0;
                        $('#installments-' + rId + ' .installment-amount-input').each(function() {
                            compTotal += parseFloat($(this).val()) || 0;
                        });
                        catTotal += compTotal;
                    }
                });

                if (catTotal > maxTotal) {
                    maxTotal = catTotal;
                }
            });

            $(this).text('₹ ' + maxTotal.toLocaleString('en-IN'));
        });
    }

    // Toast notification helper
    function showToast(message, type) {
        var toast = $('#notificationToast');
        var textSpan = $('#notificationMessage');
        
        textSpan.text(message);
        
        if (type === 'success') {
            toast.css({
                'background': '#10b981',
                'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.2)'
            });
            toast.find('i').removeClass().addClass('fas fa-check-circle');
        } else {
            toast.css({
                'background': '#ef4444',
                'box-shadow': '0 4px 12px rgba(239, 68, 68, 0.2)'
            });
            toast.find('i').removeClass().addClass('fas fa-exclamation-circle');
        }

        toast.css('display', 'flex').hide().fadeIn(300).delay(2500).fadeOut(300);
    }
</script>
@endsection
