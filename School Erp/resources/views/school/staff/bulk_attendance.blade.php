@extends('layouts.app')

@section('title', 'Staff Mark Bulk Attendance')
@section('page-title', 'Staff Mark Bulk Attendance')

@section('content')
<style>
    /* Premium CSS for Staff Bulk Attendance Page */
    .bulk-container {
        font-family: 'Inter', sans-serif;
        background: #f8f9fa;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        max-width: 100%;
        width: 100%;
        overflow: hidden;
    }

    .bulk-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .bulk-title h1 {
        font-size: 24px;
        font-weight: 800;
        color: #1a202c;
        margin: 0 0 4px 0;
    }
    
    .bulk-title p {
        font-size: 13px;
        color: #718096;
        margin: 0;
    }

    /* Filters Card Grid */
    .filters-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
    }

    .filter-col {
        display: flex;
        flex-direction: column;
    }
    .filter-col label {
        font-size: 10px;
        font-weight: 700;
        color: #718096;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.3px;
    }
    .filter-col input, .filter-col select {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        color: #1a202c;
        outline: none;
        transition: all 0.2s;
        width: 100%;
        font-weight: 600;
    }
    .filter-col input:focus, .filter-col select:focus {
        border-color: #94a3b8;
        background: #fff;
    }

    /* Notice Banner styling */
    .notice-banner {
        background: #eff6ff; /* Light blue */
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 24px;
        font-size: 13px;
        color: #2563eb;
        line-height: 1.6;
    }
    .notice-banner p {
        margin: 2px 0;
        font-weight: 600;
    }

    /* Main Grid Panel */
    .data-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        max-width: 100%;
        width: 100%;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #edf2f7;
    }
    .panel-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    /* Mustard/Brown Save Button matching design */
    .btn-save-attendance {
        background: #9a3412;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 10px 24px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: background 0.2s;
        text-decoration: none;
    }
    .btn-save-attendance:hover {
        background: #7c2d12;
    }

    /* Fix layout expansion to keep elements inside screen boundaries */
    .main {
        min-width: 0 !important;
    }
    .pg {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* Responsive grid scroll wrapper */
    .grid-scroll-wrapper {
        overflow-x: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        display: block !important;
        scrollbar-width: auto;
    }
    .grid-scroll-wrapper::-webkit-scrollbar {
        height: 10px;
        display: block;
    }
    .grid-scroll-wrapper::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 6px;
    }
    .grid-scroll-wrapper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 6px;
        border: 2px solid #f8fafc;
    }
    .grid-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    .bulk-grid-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bulk-grid-table th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 12px;
        font-weight: 700;
        color: #4a5568;
        padding: 12px 14px;
        text-align: left;
    }

    .bulk-grid-table th.date-column-header {
        text-align: center;
        min-width: 110px; /* Reduced for ultra compactness */
        max-width: 130px;
        border-left: 1px solid #e2e8f0;
        padding: 10px 8px;
    }

    .bulk-grid-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #edf2f7;
        vertical-align: top;
    }

    .employee-cell {
        min-width: 200px;
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 10;
        box-shadow: 4px 0 8px rgba(0,0,0,0.02);
    }

    .premium-table tr:hover .employee-cell {
        background: #f8fafc;
    }

    .employee-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Letter Avatar or Image Avatar */
    .employee-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ea580c; /* Orange matching Virudh letter icon */
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
    }

    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .employee-info {
        display: flex;
        flex-direction: column;
    }
    .employee-name {
        font-weight: 700;
        color: #1a202c;
        font-size: 12.5px;
    }
    .employee-sub {
        font-size: 10px;
        color: #b45309; /* Yellow text for Teaching • 02 */
        font-weight: 600;
        margin-top: 1px;
    }

    /* Date Attendance marking box */
    .date-cell-container {
        border-left: 1px solid #edf2f7;
        padding: 0 4px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .date-cell-container select {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 4px 6px;
        font-size: 11px;
        font-weight: 600;
        outline: none;
        color: #1a202c;
        height: 26px;
    }

    .time-input-wrapper {
        position: relative;
        width: 100%;
    }
    .time-input-wrapper input {
        width: 100%;
        padding: 4px 20px 4px 6px; /* Compact padding */
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 11px;
        outline: none;
        color: #1a202c;
        font-weight: 600;
        background: #fff;
        height: 26px;
    }
    .time-input-wrapper input::placeholder {
        color: #a0aec0;
        font-weight: 500;
    }
    .time-icon {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 10px;
        pointer-events: none;
    }

    /* Empty state */
    .empty-grid {
        text-align: center;
        padding: 48px;
        color: #718096;
    }
    .empty-grid i {
        font-size: 40px;
        margin-bottom: 12px;
        color: #cbd5e1;
    }

    /* ── BULK ATTENDANCE DARK MODE OVERRIDES ── */
    body.dark-mode .bulk-container {
        background: #0b0f19 !important;
        box-shadow: none !important;
    }
    body.dark-mode .bulk-title h1,
    body.dark-mode .panel-header h3 {
        color: #f8fafc !important;
    }
    body.dark-mode .bulk-title p {
        color: #cbd5e1 !important;
    }
    body.dark-mode .filters-card,
    body.dark-mode .data-panel {
        background: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .panel-header {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .filter-col label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .filter-col input,
    body.dark-mode .filter-col select {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .notice-banner {
        background: rgba(37, 99, 235, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
        color: #60a5fa !important;
    }
    body.dark-mode .bulk-grid-table th {
        background: #1f2937 !important;
        color: #cbd5e1 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .bulk-grid-table td {
        color: #f8fafc !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .employee-cell {
        background: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: 4px 0 8px rgba(0, 0, 0, 0.4) !important;
    }
    body.dark-mode .bulk-grid-table tr:hover .employee-cell,
    body.dark-mode .bulk-grid-table tr:hover td {
        background: #1f2937 !important;
    }
    body.dark-mode .employee-name {
        color: #f8fafc !important;
        font-weight: 700 !important;
    }
    body.dark-mode .employee-sub {
        color: #fbbf24 !important;
    }
    body.dark-mode .date-cell-container select,
    body.dark-mode .time-input-wrapper input {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .date-cell-container select option {
        background: #1f2937 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .time-icon {
        color: #cbd5e1 !important;
    }

    /* Status Select Dropdown */
    .status-select {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 6px;
        font-size: 11px;
        font-weight: 700;
        outline: none;
        height: 28px;
        transition: all 0.15s;
        text-align: center;
        cursor: pointer;
    }
    .status-select:focus {
        border-color: #94a3b8;
    }

    /* Dynamic select styling colors */
    .select-Present {
        color: #047857 !important;
        border-color: #a7f3d0 !important;
        background: #f0fdf4 !important;
    }
    .select-Absent {
        color: #b91c1c !important;
        border-color: #fecaca !important;
        background: #fef2f2 !important;
    }
    .select-HalfDay {
        color: #d97706 !important;
        border-color: #fde68a !important;
        background: #fffbeb !important;
    }
    .select-Leave {
        color: #1d4ed8 !important;
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
    }
    .select-Custom_Leaves {
        color: #be185d !important;
        border-color: #fbcfe8 !important;
        background: #fdf2f8 !important;
    }
    .select-not_marked {
        color: #718096 !important;
        border-color: #cbd5e1 !important;
        background: #f8fafc !important;
    }

    /* Dark Mode equivalents */
    body.dark-mode .select-Present { color: #34d399 !important; border-color: #065f46 !important; background: #064e3b !important; }
    body.dark-mode .select-Absent { color: #f87171 !important; border-color: #7f1d1d !important; background: #991b1b !important; }
    body.dark-mode .select-HalfDay { color: #fbbf24 !important; border-color: #78350f !important; background: #7c2d12 !important; }
    body.dark-mode .select-Leave { color: #60a5fa !important; border-color: #1e3a8a !important; background: #1e3a8a !important; }
    body.dark-mode .select-Custom_Leaves { color: #f472b6 !important; border-color: #9d174d !important; background: #831843 !important; }
    body.dark-mode .select-not_marked { color: #9ca3af !important; border-color: #374151 !important; background: #1f2937 !important; }
</style>

<div class="bulk-container">
    <div class="bulk-header">
        <div class="bulk-title">
            <h1>Staff Mark Bulk Attendance</h1>
            <p>Staff Management</p>
        </div>
    </div>

    <!-- Filters Card Panel -->
    <div class="filters-card">
        <form id="bulkFilterForm" method="GET" action="{{ route('school.staff.bulk-attendance') }}">
            <div class="filters-grid">
                <!-- Academic Year Dropdown -->
                <div class="filter-col">
                    <label>Academic Year *</label>
                    <select name="academic_year" onchange="document.getElementById('bulkFilterForm').submit();">
                        <option value="2025-2026">Apr 2025 - Mar 2026</option>
                    </select>
                </div>

                <!-- From Date Picker -->
                <div class="filter-col">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>

                <!-- To Date Picker -->
                <div class="filter-col">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>

                <!-- Staff Type Selector -->
                <div class="filter-col">
                    <label>Select Staff Type</label>
                    <select name="staff_type" onchange="document.getElementById('bulkFilterForm').submit();">
                        @foreach(['Teaching', 'Non Teaching', 'Driver/Supporting staff', 'Others', 'Admin'] as $t)
                            <option value="{{ $t }}" {{ $staffType === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="filter-col">
                    <label>Search Staff</label>
                    <input type="text" name="search" placeholder="Search staff" value="{{ $search }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>
            </div>
            
            <!-- Hidden filter for department if any -->
            <input type="hidden" name="department_id" value="{{ $departmentId }}">
        </form>
    </div>

    <!-- Notice Banner Info -->
    <div class="notice-banner">
        <p>Date Range: {{ date('d-m-Y', strtotime($fromDate)) }} to {{ date('d-m-Y', strtotime($toDate)) }} ({{ $totalDays }} days total - {{ $weekdays }} weekdays, {{ $weekends }} weekends)</p>
        <p>{{ $academicYearText }}</p>
        <p style="color:#d97706; font-size:12px; font-weight:700;"><i class="fas fa-exclamation-triangle"></i> Maximum allowed range: 31 days • Attendance data will load automatically</p>
    </div>

    <!-- Main Attendance Grid Form -->
    <form method="POST" action="{{ route('school.staff.bulk-attendance.post') }}">
        @csrf
        
        <div class="data-panel">
            <div class="panel-header">
                <h3>Attendance Data ({{ $staffMembers->count() }} staff members)</h3>
                <button type="submit" class="btn-save-attendance">Save Attendance</button>
            </div>

            <div class="grid-scroll-wrapper">
                @if($staffMembers->isNotEmpty())
                    <table class="bulk-grid-table">
                        <thead>
                            <tr>
                                <th class="employee-cell">Employee Details</th>
                                @foreach($datesInRange as $dObj)
                                    <th class="date-column-header">
                                        <div style="font-weight:700; color:#2d3748;">{{ $dObj->format('d M') }}</div>
                                        <div style="color:#b45309; font-size:10px; font-weight:700; margin-top:2px; text-transform:uppercase;">{{ $dObj->format('D') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffMembers as $staff)
                                @php
                                    $bgColors = ['#9a3412', '#b45309', '#047857', '#1d4ed8', '#7c3aed', '#db2777'];
                                    $bgColor = $bgColors[$staff->id % count($bgColors)];
                                @endphp
                                <tr>
                                    <td class="employee-cell">
                                        <div class="employee-profile">
                                            <div class="employee-avatar" style="background-color: {{ $bgColor }};">
                                                @if($staff->photo)
                                                    <img src="{{ $staff->photo_url }}" alt="Photo">
                                                @else
                                                    {{ substr($staff->first_name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="employee-info">
                                                <span class="employee-name">{{ $staff->full_name }}</span>
                                                <span class="employee-sub">{{ $staff->staff_type }} • {{ $staff->employee_id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @foreach($datesInRange as $dObj)
                                        @php
                                            $dateStr = $dObj->format('Y-m-d');
                                            $record = isset($attendanceMatrix[$staff->id][$dateStr]) ? $attendanceMatrix[$staff->id][$dateStr] : null;
                                            
                                            $status = 'not_marked';
                                            $clockIn = '';
                                            $clockOut = '';

                                            if ($record) {
                                                $dbStatus = strtolower($record->status);
                                                if ($dbStatus === 'present') {
                                                    $status = 'Present';
                                                } elseif ($dbStatus === 'absent') {
                                                    $status = 'Absent';
                                                } elseif ($dbStatus === 'half_day') {
                                                    $status = 'HalfDay';
                                                } elseif ($dbStatus === 'leave') {
                                                    $status = 'Leave';
                                                } elseif ($dbStatus === 'late' || $dbStatus === 'holiday') {
                                                    $status = 'Custom Leaves';
                                                }

                                                if ($record->clock_in_at) {
                                                    $clockIn = date('h:i A', strtotime($record->clock_in_at));
                                                }
                                                if ($record->clock_out_at) {
                                                    $clockOut = date('h:i A', strtotime($record->clock_out_at));
                                                }
                                            }
                                        @endphp
                                        <td>
                                            <div class="date-cell-container">
                                                <!-- Status Select dropdown -->
                                                <select name="attendance[{{ $staff->id }}][{{ $dateStr }}][status]" class="status-select" onchange="updateSelectColor(this)">
                                                    <option value="not_marked" {{ $status === 'not_marked' ? 'selected' : '' }}>Not Marked</option>
                                                    <option value="Present" {{ $status === 'Present' ? 'selected' : '' }}>Present</option>
                                                    <option value="Absent" {{ $status === 'Absent' ? 'selected' : '' }}>Absent</option>
                                                    <option value="HalfDay" {{ $status === 'HalfDay' ? 'selected' : '' }}>HalfDay</option>
                                                    <option value="Leave" {{ $status === 'Leave' ? 'selected' : '' }}>Leave</option>
                                                    <option value="Custom Leaves" {{ $status === 'Custom Leaves' ? 'selected' : '' }}>Custom Leaves</option>
                                                </select>

                                                <!-- Check In Time -->
                                                <div class="time-input-wrapper">
                                                    <input type="text" name="attendance[{{ $staff->id }}][{{ $dateStr }}][clock_in_at]" class="time-picker" placeholder="hh:mm aa" value="{{ $clockIn }}">
                                                    <i class="far fa-clock time-icon"></i>
                                                </div>

                                                <!-- Check Out Time -->
                                                <div class="time-input-wrapper">
                                                    <input type="text" name="attendance[{{ $staff->id }}][{{ $dateStr }}][clock_out_at]" class="time-picker" placeholder="hh:mm aa" value="{{ $clockOut }}">
                                                    <i class="far fa-clock time-icon"></i>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-grid">
                        <i class="fas fa-users-slash"></i>
                        <p>No active staff members found matching the selected type.</p>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Update color class of the select elements based on selected value
    function updateSelectColor(selectEl) {
        selectEl.classList.remove(
            'select-Present', 'select-Absent', 'select-HalfDay', 
            'select-Leave', 'select-Custom_Leaves', 'select-not_marked'
        );
        const val = selectEl.value.replace(' ', '_');
        selectEl.classList.add('select-' + val);
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-select').forEach(function(select) {
            updateSelectColor(select);
        });
    });
</script>
@endsection
