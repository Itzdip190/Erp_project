@extends('layouts.app')

@section('title', 'Student Mark Bulk Attendance')
@section('page-title', 'Student Mark Bulk Attendance')

@section('content')
<style>
    /* Premium CSS for Student Bulk Attendance Page */
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
        grid-template-columns: repeat(6, 1fr);
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
    .panel-header-left h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    .panel-header-left p {
        font-size: 12px;
        color: #718096;
        margin: 2px 0 0 0;
    }

    .panel-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-settings-gear {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-settings-gear:hover {
        background: #e2e8f0;
        color: #475569;
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
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
        min-width: 120px;
        max-width: 140px;
        border-left: 1px solid #e2e8f0;
        padding: 10px 8px;
    }

    /* Highlight weekend column headers */
    .bulk-grid-table th.weekend-header {
        background: #fef3c7; /* Light gold background for Sunday */
    }

    .bulk-grid-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }

    .student-cell {
        min-width: 220px;
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 10;
        box-shadow: 4px 0 8px rgba(0,0,0,0.02);
    }

    .bulk-grid-table tr:hover .student-cell {
        background: #f8fafc;
    }

    .student-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Letter Avatar */
    .student-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ea580c;
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        flex-shrink: 0;
    }

    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-info {
        display: flex;
        flex-direction: column;
    }
    .student-name {
        font-weight: 700;
        color: #1a202c;
        font-size: 12.5px;
    }
    .student-sub {
        font-size: 10px;
        color: #b45309;
        font-weight: 600;
        margin-top: 1px;
    }

    /* Date Attendance marking box */
    .date-cell-container {
        border-left: 1px solid #edf2f7;
        padding: 0 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Status Select Dropdown */
    .status-select {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 12px;
        font-weight: 700;
        outline: none;
        height: 32px;
        transition: all 0.15s;
        text-align: center;
        cursor: pointer;
    }
    .status-select:focus {
        border-color: #94a3b8;
    }

    /* Dynamic select styling colors */
    .select-present {
        color: #047857 !important;
        border-color: #a7f3d0 !important;
        background: #f0fdf4 !important;
    }
    .select-absent {
        color: #b91c1c !important;
        border-color: #fecaca !important;
        background: #fef2f2 !important;
    }
    .select-late {
        color: #d97706 !important;
        border-color: #fde68a !important;
        background: #fffbeb !important;
    }
    .select-half_day {
        color: #d97706 !important;
        border-color: #fde68a !important;
        background: #fffbeb !important;
    }
    .select-holiday {
        color: #4b5563 !important;
        border-color: #d1d5db !important;
        background: #f3f4f6 !important;
    }
    .select-leave, .select-duty_leave {
        color: #1d4ed8 !important;
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
    }
    .select-not_marked {
        color: #718096 !important;
        border-color: #cbd5e1 !important;
        background: #f8fafc !important;
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
</style>

<div class="bulk-container">
    <div class="bulk-header">
        <div class="bulk-title">
            <h1>Student Bulk Attendance</h1>
            <p>Student Management</p>
        </div>
    </div>

    @if(session('warning'))
        <div class="alert alert-warning" style="margin-bottom:20px; padding:12px 16px; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; color:#b45309; font-size:13px; font-weight:600;">
            <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>{{ session('warning') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px; padding:12px 16px; border-radius:8px; background:#f0fdf4; border:1px solid #a7f3d0; color:#15803d; font-size:13px; font-weight:600;">
            <i class="fas fa-check-circle" style="margin-right:8px;"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Filters Card Panel -->
    <div class="filters-card">
        <form id="bulkFilterForm" method="GET" action="{{ route('school.student-mgmt.bulk-attendance') }}">
            <div class="filters-grid">
                <!-- Academic Year Dropdown -->
                <div class="filter-col">
                    <label>Academic Year *</label>
                    <select name="academic_session_id" onchange="document.getElementById('bulkFilterForm').submit();">
                        @foreach($academicSessions as $ses)
                            <option value="{{ $ses->id }}" {{ $sessionId == $ses->id ? 'selected' : '' }}>{{ $ses->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Class -->
                <div class="filter-col">
                    <label>Select Class *</label>
                    <select name="class_id" id="classSelect" required onchange="filterSections(); document.getElementById('bulkFilterForm').submit();">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Section -->
                <div class="filter-col">
                    <label>Select Section *</label>
                    <select name="section_id" id="sectionSelect" required onchange="document.getElementById('bulkFilterForm').submit();">
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" data-class-id="{{ $s->class_id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
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

                <!-- Search Input -->
                <div class="filter-col">
                    <label>Search Student</label>
                    <input type="text" name="search" placeholder="Search students" value="{{ $search }}" onchange="document.getElementById('bulkFilterForm').submit();">
                </div>
            </div>
        </form>
    </div>

    <!-- Notice Banner Info -->
    @if($classId && $sectionId)
        @php
            $selectedClass = $classes->firstWhere('id', $classId);
            $selectedSection = $sections->firstWhere('id', $sectionId);
            $className = $selectedClass ? $selectedClass->name : 'N/A';
            $sectionName = $selectedSection ? $selectedSection->name : 'N/A';
        @endphp
        <div class="notice-banner">
            <p style="font-size:14px; font-weight:700; color:#1e40af; margin-bottom:6px;">Date Range: {{ date('d-m-Y', strtotime($fromDate)) }} to {{ date('d-m-Y', strtotime($toDate)) }} ({{ $totalDays }} days total - {{ $weekdays }} weekdays, {{ $weekends }} weekends)</p>
            <p style="font-weight:600; color:#1e40af;">Class: {{ $className }} - {{ $sectionName }}</p>
            <p style="font-weight:600; color:#1e40af;">{{ $academicYearText }}</p>
            <p style="color:#d97706; font-size:12px; font-weight:700;"><i class="fas fa-exclamation-triangle"></i> Maximum allowed range: 31 days • Attendance data will load automatically</p>
        </div>

        <!-- Main Attendance Grid Form -->
        <form method="POST" action="{{ route('school.student-mgmt.bulk-attendance.post') }}">
            @csrf
            <input type="hidden" name="academic_session_id" value="{{ $sessionId }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            
            <div class="data-panel">
                <div class="panel-header">
                    <div class="panel-header-left">
                        <h3>Attendance Data ({{ $students->count() }} students)</h3>
                        <p style="font-size:12px; color:#64748b;">{{ date('d-m-Y', strtotime($fromDate)) }} to {{ date('d-m-Y', strtotime($toDate)) }}</p>
                    </div>
                    <div class="panel-actions">
                        <button type="button" class="btn-settings-gear"><i class="fas fa-cog"></i></button>
                        <button type="submit" class="btn-save-attendance">Save Attendance</button>
                    </div>
                </div>

                <div class="grid-scroll-wrapper">
                    @if($students->isNotEmpty())
                        <table class="bulk-grid-table">
                            <thead>
                                <tr>
                                    <th class="student-cell">Student Details</th>
                                    @foreach($datesInRange as $dObj)
                                        @php
                                            $isWeekend = $dObj->isWeekend();
                                        @endphp
                                        <th class="date-column-header {{ $isWeekend ? 'weekend-header' : '' }}">
                                            <div style="font-weight:700; color:#2d3748;">{{ $dObj->format('d M') }}</div>
                                            <div style="color:#b45309; font-size:10px; font-weight:700; margin-top:2px; text-transform:uppercase;">
                                                {{ $dObj->format('D') }}
                                                @if($isWeekend)
                                                    <br><span style="font-size:8px; font-weight:800; color:#d97706;">Weekend</span>
                                                @endif
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $st)
                                    @php
                                        $bgColors = ['#9a3412', '#b45309', '#047857', '#1d4ed8', '#7c3aed', '#db2777'];
                                        $bgColor = $bgColors[$st->id % count($bgColors)];
                                    @endphp
                                    <tr>
                                        <td class="student-cell">
                                            <div class="student-profile">
                                                <div class="student-avatar" style="background-color: {{ $bgColor }};">
                                                    @if($st->photo)
                                                        <img src="{{ $st->photo_url }}" alt="Photo">
                                                    @else
                                                        {{ substr($st->first_name, 0, 1) }}
                                                    @endif
                                                </div>
                                                <div class="student-info">
                                                    <span class="student-name">{{ $st->full_name }}</span>
                                                    <span class="student-sub">Roll: {{ $st->roll_number ?? 'N/A' }} • ID: {{ $st->admission_number }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        @foreach($datesInRange as $dObj)
                                            @php
                                                $dateStr = $dObj->format('Y-m-d');
                                                $record = isset($attendanceMatrix[$st->id][$dateStr]) ? $attendanceMatrix[$st->id][$dateStr] : null;
                                                $status = $record ? $record->status : 'not_marked';
                                            @endphp
                                            <td>
                                                <div class="date-cell-container">
                                                    <!-- Status Select dropdown -->
                                                    <select name="attendance[{{ $st->id }}][{{ $dateStr }}][status]" class="status-select" onchange="updateSelectColor(this)">
                                                        <option value="not_marked" {{ $status === 'not_marked' ? 'selected' : '' }}>Not Marked</option>
                                                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                                        <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                                                        <option value="half_day" {{ $status === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                        <option value="holiday" {{ $status === 'holiday' ? 'selected' : '' }}>Holiday</option>
                                                        <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>Leave</option>
                                                        <option value="duty_leave" {{ $status === 'duty_leave' ? 'selected' : '' }}>Duty Leave</option>
                                                    </select>
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
                            <p>No active students found matching the selected filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    @else
        <div class="card" style="padding:48px; text-align:center;">
            <i class="fas fa-calendar-check" style="font-size:48px; color:#cbd5e1; margin-bottom:16px;"></i>
            <h3 style="color:#4a5568; margin-bottom:8px;">Please select Class and Section</h3>
            <p style="color:#718096; font-size:14px;">Select the class, section, and date range above to load the student bulk roll call sheet.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Client-side section filter based on selected class
    function filterSections() {
        const classSelect = document.getElementById('classSelect');
        const sectionSelect = document.getElementById('sectionSelect');
        const selectedClassId = classSelect.value;

        // Reset section select if class changes
        let selectedSectionStillValid = false;
        
        for (let i = 0; i < sectionSelect.options.length; i++) {
            const option = sectionSelect.options[i];
            const optionClassId = option.getAttribute('data-class-id');

            if (!selectedClassId) {
                // If no class is selected, show all section options
                option.style.display = 'block';
            } else if (!optionClassId || optionClassId === selectedClassId) {
                // Show option if it belongs to selected class
                option.style.display = 'block';
                if (option.value === sectionSelect.value) {
                    selectedSectionStillValid = true;
                }
            } else {
                // Hide option if it doesn't belong to selected class
                option.style.display = 'none';
            }
        }

        if (!selectedSectionStillValid && selectedClassId) {
            sectionSelect.value = '';
        }
    }

    // Update color class of the select elements based on selected value
    function updateSelectColor(selectEl) {
        // Remove all color classes
        selectEl.classList.remove(
            'select-present', 'select-absent', 'select-late', 
            'select-half_day', 'select-holiday', 'select-leave', 
            'select-duty_leave', 'select-not_marked'
        );
        
        // Add current selection color class
        const val = selectEl.value;
        selectEl.classList.add('select-' + val);
    }

    // Initialize select colors on document load
    document.addEventListener('DOMContentLoaded', function() {
        filterSections();
        document.querySelectorAll('.status-select').forEach(function(select) {
            updateSelectColor(select);
        });
    });
</script>
@endsection
