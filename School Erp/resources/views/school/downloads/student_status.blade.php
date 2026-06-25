@extends('layouts.app')

@section('page-title', 'Student Download Status')

@section('content')
<style>
    /* Premium Blue & White Theme Styles */
    .dl-module {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }
    .dl-title-section h1 {
        font-size: 28px !important;
        font-weight: 800 !important;
        color: #1e3a8a !important; /* Deep corporate blue */
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dl-title-section p {
        font-size: 15px !important;
        color: #475569 !important;
        font-weight: 500;
    }
    .dl-card-filter {
        background: #ffffff;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.04);
        margin-bottom: 25px;
    }
    .dl-filter-body {
        padding: 20px 24px;
    }
    .dl-form-label {
        font-size: 12px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        color: #1e40af !important; /* Blue label */
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    .dl-select, .dl-input {
        height: 46px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        border: 2px solid #dbeafe !important;
        border-radius: 8px !important;
        color: #1e293b !important;
        background-color: #f8fafc !important;
        transition: all 0.2s ease-in-out;
    }
    .dl-select:focus, .dl-input:focus {
        border-color: #2563eb !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    
    /* Action Buttons in blue-white styling */
    .dl-btn {
        height: 46px;
        padding: 0 20px;
        font-size: 15px !important;
        font-weight: 700 !important;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .dl-btn-primary {
        background: #2563eb;
        color: #ffffff !important;
        border: none;
    }
    .dl-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }
    .dl-btn-outline {
        background: #ffffff;
        color: #2563eb !important;
        border: 2px solid #2563eb;
    }
    .dl-btn-outline:hover {
        background: #eff6ff;
        transform: translateY(-1px);
    }
    .dl-btn-whatsapp {
        background: #10b981;
        color: #ffffff !important;
        border: none;
    }
    .dl-btn-whatsapp:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    /* Stats Cards with Large Font sizes & Blue accents */
    .dl-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    .dl-stat-card {
        border-radius: 12px;
        padding: 24px;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.04);
        transition: all 0.2s ease-in-out;
        border: 2px solid transparent;
    }
    .dl-stat-card:hover {
        transform: translateY(-2px);
    }
    .dl-stat-card-active-blue {
        background: #eff6ff;
        border-color: #3b82f6;
    }
    .dl-stat-card-inactive-blue {
        background: #ffffff;
        border-color: #e2e8f0;
    }
    .dl-stat-card-active-blue .stat-icon {
        background: #2563eb;
        color: #ffffff;
    }
    .dl-stat-card-inactive-blue .stat-icon {
        background: #94a3b8;
        color: #ffffff;
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-info .stat-num {
        font-size: 36px !important;
        font-weight: 850 !important;
        line-height: 1;
        color: #1e3a8a;
        margin-bottom: 4px;
    }
    .stat-info .stat-label {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #475569;
    }

    /* Table with Large Font size */
    .dl-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(30, 58, 138, 0.03);
        overflow: hidden;
    }
    .dl-table-hdr {
        background: #f8fafc;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dl-table-hdr h3 {
        font-size: 18px !important;
        font-weight: 750 !important;
        color: #1e3a8a;
        margin: 0;
    }
    .dl-badge-blue {
        background: #eff6ff;
        color: #1e40af;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px !important;
    }
    .dl-table {
        width: 100%;
        border-collapse: collapse;
    }
    .dl-table th {
        background: #1e3a8a !important; /* Premium dark blue header */
        color: #ffffff !important;
        font-size: 15px !important;
        font-weight: 750 !important;
        padding: 16px 20px !important;
        text-align: left;
    }
    .dl-table td {
        padding: 16px 20px !important;
        font-size: 16px !important; /* LARGE FONT SIZE */
        font-weight: 600 !important;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .dl-table tr:hover td {
        background: #f8fafc;
    }
    .dl-student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: bold;
        border: 2px solid #dbeafe;
    }

    /* Excel Spreadsheet Modal CSS */
    .excel-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }
    .excel-modal-container {
        width: 95vw;
        height: 90vh;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #107c41; /* Excel green accent */
    }
    .excel-modal-hdr {
        background: #107c41; /* Microsoft Excel Green color */
        color: #ffffff;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .excel-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .excel-brand i {
        font-size: 24px;
        color: #ffffff;
    }
    .excel-title-label {
        font-size: 16px !important;
        font-weight: 700 !important;
    }
    .excel-menubar {
        background: #f3f2f1;
        border-bottom: 1px solid #edebe9;
        padding: 4px 20px;
        display: flex;
        gap: 15px;
    }
    .excel-menu-item {
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #323130;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .excel-menu-item:hover {
        background: #edebe9;
    }
    .excel-toolbar {
        background: #f3f2f1;
        border-bottom: 1px solid #edebe9;
        padding: 8px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }
    .excel-sheet-viewport {
        flex-grow: 1;
        overflow: auto;
        background: #f3f2f1;
        padding: 10px;
    }
    .excel-sheet-grid {
        background: #ffffff;
        border-collapse: collapse;
        width: 100%;
        user-select: none;
    }
    .excel-sheet-grid th {
        background: #f3f2f1;
        color: #323130;
        font-size: 12px !important;
        font-weight: 600 !important;
        border: 1px solid #d2d0ce;
        padding: 6px 10px;
        text-align: center;
    }
    .excel-sheet-grid td {
        border: 1px solid #edebe9;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 8px 12px;
        color: #323130;
        outline: none;
        cursor: cell;
        min-width: 120px;
    }
    .excel-sheet-grid td.row-num {
        background: #f3f2f1;
        text-align: center;
        color: #323130;
        font-weight: 600 !important;
        min-width: 50px;
        max-width: 50px;
        border: 1px solid #d2d0ce;
    }
    .excel-sheet-grid td.active-cell {
        box-shadow: inset 0 0 0 2px #107c41;
        background: #f4faf6 !important;
    }
    .excel-sheet-grid td input {
        border: none;
        width: 100%;
        height: 100%;
        background: transparent;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        padding: 0;
        margin: 0;
    }
</style>

<div class="dl-module">
    <!-- Header -->
    <div class="dl-title-section" style="margin-bottom: 25px;">
        <h1>
            <i class="fas fa-user-graduate" style="color:#2563eb;"></i>
            Student Download Status
        </h1>
        <p>Monitor and manage student login status and mobile app download telemetry</p>
    </div>

    <!-- Filter Bar -->
    <div class="card dl-card-filter">
        <div class="card-body dl-filter-body">
            <form method="GET" action="{{ route('school.downloads.student-status') }}" style="display:flex; justify-content:space-between; align-items:flex-end; gap:20px; flex-wrap:wrap;">
                <div style="display:flex; gap:20px; flex-grow:1; flex-wrap:wrap;">
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:160px;">
                        <label class="dl-form-label">Academic Year</label>
                        <select name="academic_year" class="form-control dl-select">
                            <option value="2025-2026" selected>Apr 2025 - Mar 2026</option>
                            <option value="2024-2025">Apr 2024 - Mar 2025</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:160px;">
                        <label class="dl-form-label">Select Class</label>
                        <select name="class_id" class="form-control dl-select" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; flex:1; min-width:160px;">
                        <label class="dl-form-label">Select Section</label>
                        <select name="section_id" class="form-control dl-select" onchange="this.form.submit()">
                            <option value="">All Sections</option>
                            @foreach($sections as $s)
                                <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="dl-btn dl-btn-outline" onclick="openExcelModal()">
                        <i class="fas fa-eye"></i> VIEW
                    </button>
                    <div class="dropdown-wrapper" style="position:relative; display:inline-block;">
                        <button type="button" class="dl-btn dl-btn-outline" onclick="toggleDropdown()">
                            <i class="fas fa-download"></i> DOWNLOAD <i class="fas fa-caret-down"></i>
                        </button>
                        <div id="dlDropdown" style="display:none; position:absolute; right:0; top:100%; background:white; border:1px solid #cbd5e1; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.1); z-index:100; min-width:180px; margin-top:5px; overflow:hidden;">
                            <a href="#" style="display:block; padding:12px 16px; color:#1e293b; font-size:14px; text-decoration:none; font-weight:600;" onclick="event.preventDefault(); dlPrintPDF(); closeDropdown();"><i class='fas fa-file-pdf' style='color:#e11d48;margin-right:6px;'></i>Export PDF</a>
                            <a href="#" style="display:block; padding:12px 16px; color:#1e293b; font-size:14px; text-decoration:none; font-weight:600;" onclick="event.preventDefault(); exportExcelCSV(); closeDropdown();"><i class='fas fa-file-csv' style='color:#107c41;margin-right:6px;'></i>Export CSV / Excel</a>
                        </div>
                    </div>
                    <button type="button" class="dl-btn dl-btn-outline" onclick="showToast('SMS notification successfully queued for dispatch!')">
                        <i class="fas fa-envelope"></i> SEND SMS TO ALL
                    </button>
                    <button type="button" class="dl-btn dl-btn-whatsapp" onclick="showToast('WhatsApp notification successfully queued for dispatch!')">
                        <i class="fab fa-whatsapp"></i> WHATSAPP TO ALL
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle Cards Grid -->
    <div class="dl-stats-grid">
        <!-- Logged In Card -->
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'logged_in']) }}" class="dl-stat-card {{ $tab === 'logged_in' ? 'dl-stat-card-active-blue' : 'dl-stat-card-inactive-blue' }}">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num">{{ $loggedIn->count() }}</div>
                <div class="stat-label">Students who have logged in</div>
            </div>
        </a>

        <!-- Haven't Logged In Card -->
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'not_logged_in']) }}" class="dl-stat-card {{ $tab === 'not_logged_in' ? 'dl-stat-card-active-blue' : 'dl-stat-card-inactive-blue' }}">
            <div class="stat-icon" style="background:#ef4444;">
                <i class="fas fa-times"></i>
            </div>
            <div class="stat-info">
                <div class="stat-num" style="color:#b91c1c;">{{ $notLoggedIn->count() }}</div>
                <div class="stat-label">Students who haven't logged in</div>
            </div>
        </a>
    </div>

    <!-- Table Listing -->
    <div class="dl-table-card">
        <div class="dl-table-hdr">
            <h3>Listing: {{ $tab === 'logged_in' ? 'Logged In Students' : "Haven't Logged In Students" }}</h3>
            <span class="dl-badge-blue">{{ $totalItems }} Records</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="dl-table">
                <thead>
                    <tr>
                        <th style="width:80px; text-align:center;">#</th>
                        <th>Student Name</th>
                        <th>Father's Contact Number</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedList as $index => $st)
                        <tr>
                            <td style="text-align:center; color:#64748b; font-weight:700;">
                                {{ sprintf('%02d.', ($page - 1) * 12 + $loop->iteration) }}
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="dl-student-avatar">
                                        {{ substr($st->first_name, 0, 1) }}{{ substr($st->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: #1e3a8a;">{{ $st->full_name }}</div>
                                        <small style="color: #64748b; font-size:12px;">Admission ID: {{ $st->admission_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 700; color: #334155;">
                                {{ $st->guardian_phone ?? '—' }}
                            </td>
                            <td>
                                <span class="dl-badge-blue">
                                    {{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:50px; color:#64748b;">
                                <i class="fas fa-user-circle" style="font-size:40px; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
                                No student records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="padding:15px 24px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-weight: 700; color: #475569; font-size: 14px;">
                @if($totalItems > 0)
                    Showing {{ ($page - 1) * 12 + 1 }}–{{ min($page * 12, $totalItems) }} of {{ $totalItems }} Records
                @else
                    No Records
                @endif
            </div>
            @if($totalPages > 1)
            <div style="display:flex; gap:5px; align-items:center; flex-wrap:wrap;">
                @if($page > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;"><i class="fas fa-chevron-left"></i></a>
                @else
                    <button class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;" disabled><i class="fas fa-chevron-left"></i></button>
                @endif
                @for($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++)
                    @if($p === $page)
                        <button class="dl-btn dl-btn-primary" style="height:34px; padding:0 12px; font-size:12px !important;">{{ $p }}</button>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}" class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;">{{ $p }}</a>
                    @endif
                @endfor
                @if($page < $totalPages)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;"><i class="fas fa-chevron-right"></i></a>
                @else
                    <button class="dl-btn dl-btn-outline" style="height:34px; padding:0 12px; font-size:12px !important;" disabled><i class="fas fa-chevron-right"></i></button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Excel Spreadsheet Modal -->
<div id="excelModal" class="excel-modal">
    <div class="excel-modal-container">
        <!-- Header -->
        <div class="excel-modal-hdr">
            <div class="excel-brand">
                <i class="fas fa-file-excel"></i>
                <span class="excel-title-label">Excel Online Viewer - Student Download Status</span>
            </div>
            <button class="dl-btn" style="background:#b91c1c; color:white; border:none; height:32px; padding:0 12px; font-size:12px !important;" onclick="closeExcelModal()">
                <i class="fas fa-times"></i> Close Spreadsheet
            </button>
        </div>

        <!-- Menubar -->
        <div class="excel-menubar">
            <span class="excel-menu-item">File</span>
            <span class="excel-menu-item">Home</span>
            <span class="excel-menu-item">Insert</span>
            <span class="excel-menu-item">Data</span>
            <span class="excel-menu-item">Review</span>
            <span class="excel-menu-item">View</span>
            <span class="excel-menu-item" style="color:#107c41; font-weight:700 !important;">Save Changes</span>
        </div>

        <!-- Toolbar -->
        <div class="excel-toolbar">
            <div style="display:flex; gap:10px; align-items:center;">
                <input type="text" id="excelSearch" class="form-control dl-input" style="height:34px !important; padding:4px 10px !important; font-size:13px !important; width:220px;" placeholder="Filter spreadsheet records..." onkeyup="filterExcelGrid()">
                <span style="font-size:12px; color:#595959; font-weight:600;"><i class="fas fa-info-circle"></i> Double-click cells to edit cell values inline.</span>
            </div>
            <button class="dl-btn" style="background:#107c41; color:white; border:none; height:34px; padding:0 16px; font-size:13px !important;" onclick="exportExcelCSV()">
                <i class="fas fa-file-csv"></i> Export CSV
            </button>
        </div>

        <!-- Viewport Grid -->
        <div class="excel-sheet-viewport">
            <table class="excel-sheet-grid" id="excelGrid">
                <thead>
                    <tr>
                        <th style="width:50px;"></th>
                        <th style="width:50px;">A</th>
                        <th>B</th>
                        <th>C</th>
                        <th>D</th>
                        <th>E</th>
                    </tr>
                    <tr style="background:#f3f2f1;">
                        <th></th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Admission ID</th>
                        <th>Father's Contact</th>
                        <th>Class & Section</th>
                    </tr>
                </thead>
                <tbody id="excelBody">
                    @foreach($paginatedList as $index => $st)
                        <tr>
                            <td class="row-num">{{ $index + 1 }}</td>
                            <td onclick="selectCell(this)" ondblclick="editCell(this)">{{ $st->roll_number ?? ($index + 1) }}</td>
                            <td onclick="selectCell(this)" ondblclick="editCell(this)">{{ $st->full_name }}</td>
                            <td onclick="selectCell(this)" ondblclick="editCell(this)">{{ $st->admission_number }}</td>
                            <td onclick="selectCell(this)" ondblclick="editCell(this)">{{ $st->guardian_phone ?? '—' }}</td>
                            <td onclick="selectCell(this)" ondblclick="editCell(this)">{{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Stats Dropdown toggle
    function toggleDropdown() {
        var dd = document.getElementById('dlDropdown');
        dd.style.display = (dd.style.display === 'none' || dd.style.display === '') ? 'block' : 'none';
    }
    function closeDropdown() {
        document.getElementById('dlDropdown').style.display = 'none';
    }
    window.onclick = function(event) {
        if (!event.target.matches('.dl-btn') && !event.target.closest('.dropdown-wrapper')) {
            closeDropdown();
        }
    }

    // Print-to-PDF
    function dlPrintPDF() {
        window.print();
    }

    // Excel Modal Controls
    function openExcelModal() {
        document.getElementById('excelModal').style.display = 'flex';
    }
    function closeExcelModal() {
        document.getElementById('excelModal').style.display = 'none';
    }

    // Active Cell Selection Highlight
    let currentSelectedCell = null;
    function selectCell(cell) {
        if (currentSelectedCell) {
            currentSelectedCell.classList.remove('active-cell');
        }
        currentSelectedCell = cell;
        cell.classList.add('active-cell');
    }

    // Double click to edit cell value
    function editCell(cell) {
        if (cell.querySelector('input')) return;
        
        let originalVal = cell.innerText;
        cell.innerHTML = `<input type="text" value="${originalVal}">`;
        let input = cell.querySelector('input');
        input.focus();
        
        // select all text
        input.select();

        input.onblur = function() {
            cell.innerText = input.value;
        };

        input.onkeydown = function(e) {
            if (e.key === 'Enter') {
                cell.innerText = input.value;
            } else if (e.key === 'Escape') {
                cell.innerText = originalVal;
            }
        };
    }

    // Local filter search inside Excel Sheet grid
    function filterExcelGrid() {
        let val = document.getElementById('excelSearch').value.toLowerCase();
        let rows = document.getElementById('excelBody').getElementsByTagName('tr');
        for (let row of rows) {
            let cells = row.getElementsByTagName('td');
            let match = false;
            // skip first cell (row-num)
            for (let i = 1; i < cells.length; i++) {
                if (cells[i].innerText.toLowerCase().indexOf(val) > -1) {
                    match = true;
                    break;
                }
            }
            row.style.display = match ? '' : 'none';
        }
    }

    // Export Excel Sheet grid to CSV
    function exportExcelCSV() {
        let rows = document.getElementById('excelGrid').getElementsByTagName('tr');
        let csvContent = "";
        
        for (let row of rows) {
            let cells = row.querySelectorAll('th, td');
            let rowContent = [];
            // Skip first col (row num / header spacer)
            for (let i = 1; i < cells.length; i++) {
                let cellText = cells[i].innerText.replace(/"/g, '""');
                rowContent.push(`"${cellText}"`);
            }
            csvContent += rowContent.join(",") + "\n";
        }
        
        // Create download element
        let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        let url = URL.createObjectURL(blob);
        let link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "Student_Download_Status.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        showToast("Spreadsheet successfully exported to CSV!");
    }
</script>
@endsection
