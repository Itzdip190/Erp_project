@extends('layouts.app')

@section('title', 'Student Report')

@section('content')
<style>
    /* Premium Visual Design Styles for Student Report */
    .report-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }
    .report-header-title h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: var(--navy);
        margin: 0;
    }
    .report-header-title p {
        font-size: 12px;
        color: var(--t2);
        margin: 4px 0 0 0;
    }
    .report-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .report-grid {
            grid-template-columns: 1fr;
        }
    }
    .report-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }
    .report-card-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .report-card-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--navy);
        font-family: 'Plus Jakarta Sans', sans-serif;
        text-transform: capitalize;
    }
    .report-card-actions {
        display: flex;
        gap: 12px;
        color: #f59e0b;
        font-size: 13px;
    }
    .report-card-actions i {
        cursor: pointer;
        transition: opacity 0.2s, transform 0.2s;
    }
    .report-card-actions i:hover {
        opacity: 0.8;
        transform: scale(1.1);
    }
    
    /* Total Students Emerald Box */
    .total-students-box {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff;
        border-radius: 14px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 120px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.15);
    }
    .total-students-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
        margin-bottom: 6px;
    }
    .total-students-value {
        font-size: 38px;
        font-weight: 800;
        font-family: 'Plus Jakarta Sans', sans-serif;
        line-height: 1;
    }

    /* Filters Layout */
    .filters-box {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    @media (max-width: 600px) {
        .filters-box {
            grid-template-columns: 1fr;
        }
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--t2);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .filter-select-wrapper {
        position: relative;
    }
    .filter-select-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 12px;
        z-index: 5;
    }
    .filter-select {
        width: 100%;
        height: 38px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0 12px 0 34px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--t1);
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s;
        appearance: none;
    }
    .filter-select:focus {
        border-color: #f59e0b;
    }

    /* Stacked Progress Bar Styles */
    .stacked-bar-container {
        margin-bottom: 6px;
    }
    .stacked-bar-text-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 10.5px;
        font-weight: 600;
        color: var(--t2);
        margin-bottom: 8px;
    }
    .stacked-bar {
        display: flex;
        height: 26px;
        background: #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 12px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .stacked-segment {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10.5px;
        font-weight: 700;
        color: #fff;
        transition: width 0.3s ease;
    }
    .legend-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 16px;
        margin-top: 4px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 600;
        color: var(--t2);
    }
    .legend-color-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }

    /* Color Segments */
    .color-fees-1 { background-color: #7c3aed; }
    .color-fees-2 { background-color: #f43f5e; }
    .color-fees-unmapped { background-color: #1e1b4b; }

    .color-admission-old { background-color: #f97316; }
    .color-admission-new { background-color: #0d9488; }
    .color-admission-unmapped { background-color: #9ca3af; }

    .color-gender-male { background-color: #7c3aed; }
    .color-gender-female { background-color: #fb923c; }
    .color-gender-other { background-color: #9ca3af; }
    .color-gender-unmapped { background-color: #0d9488; }

    .chart-container {
        position: relative;
        height: 220px;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

@php
    // Percentage Calculations for Stacked Bars
    $sumFee = $feeSchedule1 + $feeSchedule2 + $notMappedFee;
    $feeSchedule1Pct = $sumFee > 0 ? round(($feeSchedule1 / $sumFee) * 100, 2) : 0;
    $feeSchedule2Pct = $sumFee > 0 ? round(($feeSchedule2 / $sumFee) * 100, 2) : 0;
    $notMappedFeePct = $sumFee > 0 ? round(($notMappedFee / $sumFee) * 100, 2) : 0;

    $sumAdmission = $newCount + $oldCount + $notMappedAdmission;
    $newCountPct = $sumAdmission > 0 ? round(($newCount / $sumAdmission) * 100, 2) : 0;
    $oldCountPct = $sumAdmission > 0 ? round(($oldCount / $sumAdmission) * 100, 2) : 0;
    $notMappedAdmissionPct = $sumAdmission > 0 ? round(($notMappedAdmission / $sumAdmission) * 100, 2) : 0;

    $sumGender = $maleCount + $femaleCount + $otherCount + $notMappedGender;
    $maleCountPct = $sumGender > 0 ? round(($maleCount / $sumGender) * 100, 2) : 0;
    $femaleCountPct = $sumGender > 0 ? round(($femaleCount / $sumGender) * 100, 2) : 0;
    $otherCountPct = $sumGender > 0 ? round(($otherCount / $sumGender) * 100, 2) : 0;
    $notMappedGenderPct = $sumGender > 0 ? round(($notMappedGender / $sumGender) * 100, 2) : 0;
@endphp

<!-- Header Row -->
<div class="report-header-row">
    <div class="report-header-title">
        <h1>Student Report</h1>
        <p>Student Management</p>
    </div>
    
    <div style="display:flex; align-items:center; gap:12px;">
        <!-- Academic Session Filter -->
        <form method="GET" action="{{ route('school.student-mgmt.report') }}" id="reportFilterForm" style="display:flex; align-items:center; gap:12px;">
            <div class="filter-group" style="width: 180px;">
                <div class="filter-select-wrapper">
                    <i class="far fa-calendar-alt"></i>
                    <select name="academic_session_id" class="filter-select" onchange="document.getElementById('reportFilterForm').submit()">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ $sessionId == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Hide standard selects to append them via JS or keep them in the form -->
            <input type="hidden" name="class_section_id" value="{{ $classSectionId }}">
            <input type="hidden" name="is_active" value="{{ $isActive }}">
            <input type="hidden" name="admission_type" value="{{ $admissionType }}">
        </form>

        <button onclick="window.print()" class="btn-export" style="background: #c28b18; border: none; display: flex; align-items: center; gap: 6px; font-weight: 700;">
            <i class="fas fa-plus"></i> CREATE REPORT
        </button>
    </div>
</div>

<!-- Main Grid -->
<div class="report-grid">
    <!-- LEFT COLUMN -->
    <div class="report-col-left">
        <!-- Total Students Emerald Card -->
        <div class="total-students-box">
            <span class="total-students-label">Total Students</span>
            <span class="total-students-value">{{ $totalStudentsCount }}</span>
        </div>

        <!-- Fee Schedule Split Card -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">Fee Schedule Split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="stacked-bar-container">
                <div class="stacked-bar-text-row">
                    <span style="color:#7c3aed;">• FEES SCHEDULE 1: {{ $feeSchedule1 }}</span>
                    <span style="color:#f43f5e;">• FEES SCHEDULE 2: {{ $feeSchedule2 }}</span>
                    <span style="color:#1e1b4b;">• NOT MAPPED: {{ $notMappedFee }}</span>
                </div>
                <div class="stacked-bar">
                    @if($feeSchedule1Pct > 0)
                        <div class="stacked-segment color-fees-1" style="width: {{ $feeSchedule1Pct }}%;">{{ $feeSchedule1Pct }}%</div>
                    @endif
                    @if($feeSchedule2Pct > 0)
                        <div class="stacked-segment color-fees-2" style="width: {{ $feeSchedule2Pct }}%;">{{ $feeSchedule2Pct }}%</div>
                    @endif
                    @if($notMappedFeePct > 0)
                        <div class="stacked-segment color-fees-unmapped" style="width: {{ $notMappedFeePct }}%;">{{ $notMappedFeePct }}%</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admission Split Card -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">Admission split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="stacked-bar-container">
                <div class="stacked-bar-text-row">
                    <span style="color:#f97316;">• OLD: {{ $oldCount }}</span>
                    <span style="color:#0d9488;">• NEW: {{ $newCount }}</span>
                    <span style="color:#9ca3af;">• NOT MAPPED: {{ $notMappedAdmission }}</span>
                </div>
                <div class="stacked-bar">
                    @if($oldCountPct > 0)
                        <div class="stacked-segment color-admission-old" style="width: {{ $oldCountPct }}%;">{{ $oldCountPct }}%</div>
                    @endif
                    @if($newCountPct > 0)
                        <div class="stacked-segment color-admission-new" style="width: {{ $newCountPct }}%;">{{ $newCountPct }}%</div>
                    @endif
                    @if($notMappedAdmissionPct > 0)
                        <div class="stacked-segment color-admission-unmapped" style="width: {{ $notMappedAdmissionPct }}%;">{{ $notMappedAdmissionPct }}%</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Category Split Donut -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">Category split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="categorySplitChart"></canvas>
            </div>
        </div>

        <!-- Age Split Vertical Bar -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">Age split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="chart-container" style="height: 280px;">
                <canvas id="ageSplitChart"></canvas>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="report-col-right">
        <!-- Interactive Dropdowns card -->
        <div class="report-card" style="padding: 15px 20px;">
            <form method="GET" action="{{ route('school.student-mgmt.report') }}" id="dropdownFilterForm">
                <input type="hidden" name="academic_session_id" value="{{ $sessionId }}">
                <div class="filters-box" style="grid-template-columns: 1fr; gap: 14px; margin-bottom: 0;">
                    <!-- Class & Section Dropdown -->
                    <div class="filter-group">
                        <div class="filter-select-wrapper">
                            <i class="fas fa-book"></i>
                            <select name="class_section_id" class="filter-select" onchange="document.getElementById('dropdownFilterForm').submit()">
                                <option value="all" {{ $classSectionId === 'all' ? 'selected' : '' }}>Class & Section (All)</option>
                                @foreach($classesAndSections as $class)
                                    @foreach($class->sections as $sec)
                                        <option value="{{ $class->id }}-{{ $sec->id }}" {{ $classSectionId === "{$class->id}-{$sec->id}" ? 'selected' : '' }}>
                                            {{ $class->name }} - {{ $sec->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Active Dropdown -->
                    <div class="filter-group">
                        <div class="filter-select-wrapper">
                            <i class="fas fa-user-check"></i>
                            <select name="is_active" class="filter-select" onchange="document.getElementById('dropdownFilterForm').submit()">
                                <option value="all" {{ $isActive === 'all' ? 'selected' : '' }}>All Statuses</option>
                                <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Admission Type Dropdown -->
                    <div class="filter-group">
                        <div class="filter-select-wrapper">
                            <i class="fas fa-user-plus"></i>
                            <select name="admission_type" class="filter-select" onchange="document.getElementById('dropdownFilterForm').submit()">
                                <option value="all" {{ $admissionType === 'all' ? 'selected' : '' }}>Admission Type (All)</option>
                                <option value="new" {{ $admissionType === 'new' ? 'selected' : '' }}>New</option>
                                <option value="old" {{ $admissionType === 'old' ? 'selected' : '' }}>Old</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Gender Split Card -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">Gender split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="stacked-bar-container">
                <div class="stacked-bar-text-row">
                    <span style="color:#7c3aed;">• MALE: {{ $maleCount }}</span>
                    <span style="color:#fb923c;">• FEMALE: {{ $femaleCount }}</span>
                    <span style="color:#9ca3af;">• OTHER: {{ $otherCount }}</span>
                    <span style="color:#0d9488;">• NOT MAPPED: {{ $notMappedGender }}</span>
                </div>
                <div class="stacked-bar">
                    @if($maleCountPct > 0)
                        <div class="stacked-segment color-gender-male" style="width: {{ $maleCountPct }}%;">{{ $maleCountPct }}%</div>
                    @endif
                    @if($femaleCountPct > 0)
                        <div class="stacked-segment color-gender-female" style="width: {{ $femaleCountPct }}%;">{{ $femaleCountPct }}%</div>
                    @endif
                    @if($otherCountPct > 0)
                        <div class="stacked-segment color-gender-other" style="width: {{ $otherCountPct }}%;">{{ $otherCountPct }}%</div>
                    @endif
                    @if($notMappedGenderPct > 0)
                        <div class="stacked-segment color-gender-unmapped" style="width: {{ $notMappedGenderPct }}%;">{{ $notMappedGenderPct }}%</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Religion Split Donut -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">Religion split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="religionSplitChart"></canvas>
            </div>
        </div>

        <!-- House-wise Split Donut -->
        <div class="report-card">
            <div class="report-card-hdr">
                <span class="report-card-title">House-wise split</span>
                <div class="report-card-actions">
                    <i class="far fa-eye" title="View Details"></i>
                    <i class="fas fa-download" title="Download Details"></i>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="houseSplitChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // -------------------------------------------------------------
    // 1. Category Split Donut Chart
    // -------------------------------------------------------------
    const categoryCtx = document.getElementById('categorySplitChart').getContext('2d');
    
    // Parse category data from PHP
    const catLabels = [];
    const catData = [];
    @foreach($categoryCounts as $name => $count)
        catLabels.push("{{ $name }}");
        catData.push({{ $count }});
    @endforeach
    
    // Push "Not mapped"
    catLabels.push("NOT MAPPED");
    catData.push({{ $notMappedCategory }});
    
    // Calculate total for label percentages
    const totalCat = catData.reduce((a, b) => a + b, 0);
    const catLabelsWithPct = catLabels.map((lbl, idx) => {
        const val = catData[idx];
        const pct = totalCat > 0 ? ((val / totalCat) * 100).toFixed(2) : 0;
        return `${lbl}: ${pct}%`;
    });

    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: catLabelsWithPct,
            datasets: [{
                data: catData,
                backgroundColor: [
                    '#3b82f6', // General
                    '#14b8a6', // OBC
                    '#ec4899', // SC
                    '#8b5cf6', // ST
                    '#fb923c', // BC
                    '#84cc16'  // NOT MAPPED
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        padding: 10,
                        font: { size: 9.5, weight: '600' }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // -------------------------------------------------------------
    // 2. Religion Split Donut Chart
    // -------------------------------------------------------------
    const religionCtx = document.getElementById('religionSplitChart').getContext('2d');
    const relData = [{{ $sikhCount }}, {{ $hinduCount }}, {{ $notMappedReligion }}];
    const totalRel = relData.reduce((a, b) => a + b, 0);
    const relLabels = [
        `SIKH: ${totalRel > 0 ? ((relData[0] / totalRel) * 100).toFixed(2) : 0}%`,
        `HINDU: ${totalRel > 0 ? ((relData[1] / totalRel) * 100).toFixed(2) : 0}%`,
        `Not mapped: ${totalRel > 0 ? ((relData[2] / totalRel) * 100).toFixed(2) : 0}%`
    ];

    new Chart(religionCtx, {
        type: 'doughnut',
        data: {
            labels: relLabels,
            datasets: [{
                data: relData,
                backgroundColor: [
                    '#374151', // Sikh (dark grey)
                    '#0284c7', // Hindu (blue)
                    '#0d9488'  // Not mapped (teal)
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        padding: 10,
                        font: { size: 9.5, weight: '600' }
                    }
                }
            },
            cutout: '60%'
        }
    });

    // -------------------------------------------------------------
    // 3. Age Split Vertical Bar Chart
    // -------------------------------------------------------------
    const ageCtx = document.getElementById('ageSplitChart').getContext('2d');
    const ageLabels = ['Not Mapped', '4-8', '8-12', '12-16', '16-20', '20-24', '24-28'];
    const ageData = [
        {{ $ageGroups['Not Mapped'] }},
        {{ $ageGroups['4-8'] }},
        {{ $ageGroups['8-12'] }},
        {{ $ageGroups['12-16'] }},
        {{ $ageGroups['16-20'] }},
        {{ $ageGroups['20-24'] }},
        {{ $ageGroups['24-28'] }}
    ];

    new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: ageLabels,
            datasets: [{
                label: 'Students',
                data: ageData,
                backgroundColor: [
                    '#facc15', // Not Mapped (Yellow)
                    '#0d9488', // 4-8 (Teal)
                    '#f97316', // 8-12 (Orange)
                    '#facc15', // 12-16 (Yellow)
                    '#0d9488', // 16-20 (Teal)
                    '#0d9488', // 20-24 (Teal)
                    '#facc15'  // 24-28 (Yellow)
                ],
                borderRadius: 6,
                barThickness: 28
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600' } }
                }
            }
        }
    });

    // -------------------------------------------------------------
    // 4. House-wise Split Donut Chart
    // -------------------------------------------------------------
    const houseCtx = document.getElementById('houseSplitChart').getContext('2d');
    
    // Parse house data
    const houseLabels = [];
    const houseData = [];
    @foreach($houseCounts as $name => $count)
        houseLabels.push("{{ $name }}");
        houseData.push({{ $count }});
    @endforeach
    
    // Push "Not mapped"
    houseLabels.push("Not Mapped");
    houseData.push({{ $notMappedHouse }});
    
    const totalHouse = houseData.reduce((a, b) => a + b, 0);
    const houseLabelsWithPct = houseLabels.map((lbl, idx) => {
        const val = houseData[idx];
        const pct = totalHouse > 0 ? ((val / totalHouse) * 100).toFixed(2) : 0;
        return `${lbl}-${pct}%`;
    });

    new Chart(houseCtx, {
        type: 'doughnut',
        data: {
            labels: houseLabelsWithPct,
            datasets: [{
                data: houseData,
                backgroundColor: [
                    '#06b6d4', // Cyan
                    '#a855f7', // Purple
                    '#ec4899', // Pink
                    '#0d9488'  // Not Mapped (Teal)
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        padding: 10,
                        font: { size: 9.5, weight: '600' }
                    }
                }
            },
            cutout: '60%'
        }
    });
});
</script>
@endsection
