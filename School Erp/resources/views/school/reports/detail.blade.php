@extends('layouts.app')

@section('title', $title)

@section('styles')
<style>
    :root {
        --det-primary: #3b82f6;
        --det-dark: #1e3a8a;
        --det-light: #eff6ff;
        --det-text: #1e293b;
        --det-text-muted: #64748b;
        --det-border: #cbd5e1;
        --det-card-bg: #ffffff;
    }
    body.dark-mode {
        --det-primary: #38bdf8;
        --det-dark: #0f172a;
        --det-light: rgba(56, 189, 248, 0.08);
        --det-text: #f8fafc;
        --det-text-muted: #94a3b8;
        --det-border: #374151;
        --det-card-bg: #111827;
    }

    .det-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 8px 30px 8px;
    }

    /* Header & Back Link */
    .det-back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--det-primary);
        text-decoration: none !important;
        margin-bottom: 20px;
        transition: transform 0.2s;
    }
    .det-back-link:hover {
        transform: translateX(-4px);
    }
    .det-hdr {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .det-hdr-left h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--det-text);
        margin: 0 0 4px 0;
    }
    .det-hdr-left p {
        font-size: 13px;
        color: var(--det-text-muted);
        margin: 0;
    }
    .det-hdr-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Action Buttons */
    .det-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--det-border);
        background: var(--det-card-bg);
        color: var(--det-text);
        transition: all 0.2s;
    }
    .det-btn:hover {
        background: var(--det-light);
        border-color: var(--det-primary);
    }
    .det-btn-primary {
        background: var(--det-primary);
        color: #ffffff;
        border-color: var(--det-primary);
    }
    .det-btn-primary:hover {
        background: #2563eb;
        color: #ffffff;
    }
    .det-btn-success {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }
    .det-btn-success:hover {
        background: #047857;
        color: #ffffff;
    }

    /* Filters Display Bar */
    .det-filter-bar {
        background: var(--det-light);
        border: 1px solid var(--det-border);
        border-radius: 12px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .det-filter-info {
        font-size: 13px;
        font-weight: 600;
        color: var(--det-text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .det-filter-tag {
        background: var(--det-card-bg);
        border: 1px solid var(--det-border);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--det-primary);
    }

    /* Stat Cards */
    .det-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .det-stat-card {
        background: var(--det-card-bg);
        border: 1px solid var(--det-border);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .det-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .det-stat-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--det-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .det-stat-val {
        font-size: 18px;
        font-weight: 800;
        color: var(--det-text);
    }

    /* Card Wrapper & Table */
    .det-card {
        background: var(--det-card-bg);
        border: 1px solid var(--det-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .det-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--det-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .det-search-wrap {
        position: relative;
        width: 100%;
        max-width: 300px;
    }
    .det-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--det-text-muted);
    }
    .det-search-input {
        width: 100%;
        height: 38px;
        padding-left: 36px;
        border: 1px solid var(--det-border);
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        background: var(--det-card-bg);
        color: var(--det-text);
    }
    .det-search-input:focus {
        border-color: var(--det-primary);
    }

    .det-table-container {
        overflow-x: auto;
        max-height: 500px;
        overflow-y: auto;
    }
    .det-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .det-table th {
        background: var(--det-light);
        color: var(--det-text);
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 2px solid var(--det-border);
        position: sticky;
        top: 0;
        z-index: 10;
        cursor: pointer;
        user-select: none;
    }
    .det-table th i {
        font-size: 10px;
        margin-left: 4px;
        color: var(--det-text-muted);
    }
    .det-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--det-border);
        color: var(--det-text);
        white-space: nowrap;
    }
    .det-table tr:hover {
        background: rgba(0, 0, 0, 0.01);
    }
    body.dark-mode .det-table tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .det-empty-state {
        padding: 40px;
        text-align: center;
        color: var(--det-text-muted);
    }
    .det-empty-state i {
        font-size: 40px;
        margin-bottom: 12px;
        color: var(--det-border);
    }

    /* Modal Styling */
    .det-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1100;
        align-items: center;
        justify-content: center;
    }
    .det-modal.open {
        display: flex;
    }
    .det-modal-card {
        background: var(--det-card-bg);
        border: 1px solid var(--det-border);
        border-radius: 12px;
        width: 100%;
        max-width: 480px;
        box-shadow: var(--det-shadow-lg);
        overflow: hidden;
        animation: detModalOpen 0.2s ease-out;
    }
    @keyframes detModalOpen {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .det-modal-hdr {
        background: var(--det-light);
        padding: 16px 20px;
        border-bottom: 1px solid var(--det-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .det-modal-hdr h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--det-text);
        margin: 0;
    }
    .det-modal-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: var(--det-text-muted);
    }
    .det-modal-body {
        padding: 20px;
    }
    .det-form-group {
        margin-bottom: 16px;
    }
    .det-form-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--det-text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }
    .det-form-control {
        width: 100%;
        height: 38px;
        border: 1px solid var(--det-border);
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13px;
        outline: none;
        background: var(--det-card-bg);
        color: var(--det-text);
    }
    .det-form-control:focus {
        border-color: var(--det-primary);
    }

    /* Column Checklist layout */
    .det-col-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }
    .det-col-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--det-text);
        cursor: pointer;
    }
    .det-col-item input {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    /* ─── PRINT CSS STYLES ───────────────────────────────── */
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        /* Hide navbar, sidebar, chatbot, translate element, buttons, filters */
        .sidebar,
        .sidebar-stitch,
        .sb-nav,
        .sb-logo,
        .sb-school-header,
        .topbar,
        .page-hdr,
        .det-back-link,
        .det-hdr-right,
        .det-filter-bar,
        .no-print,
        .no-print *,
        #robot-assistant,
        .robot-body,
        #chat-container,
        .chat-container,
        .chat-wrapper,
        .chat-window,
        .yash-ai-bubble,
        .skiptranslate,
        .goog-te-banner-frame,
        .goog-te-gadget,
        iframe,
        #google_translate_element {
            display: none !important;
            height: 0 !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
        .main {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .det-container {
            max-width: 100% !important;
            padding: 0 !important;
        }
        .det-card {
            border: none !important;
            box-shadow: none !important;
        }
        .det-table-container {
            max-height: none !important;
            overflow: visible !important;
        }
        .det-table {
            border: 1px solid #000000 !important;
        }
        .det-table th {
            background: #f1f5f9 !important;
            color: #000000 !important;
            border-bottom: 2px solid #000000 !important;
        }
        .det-table td {
            border-bottom: 1px solid #000000 !important;
            color: #000000 !important;
            white-space: normal !important;
        }
        /* Print-only school header */
        .print-school-header {
            display: flex !important;
            align-items: center;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 2px solid #000000;
            margin-bottom: 20px;
        }
        .print-school-header img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
        .print-school-header .psh-info h2 {
            font-size: 18px;
            font-weight: 800;
            color: #000;
            margin: 0 0 3px 0;
        }
        .print-school-header .psh-info p {
            font-size: 11px;
            color: #555;
            margin: 0;
        }
        .print-report-title {
            text-align: center;
            margin-bottom: 16px;
        }
        .print-report-title h3 {
            font-size: 16px;
            font-weight: 800;
            color: #000;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .print-report-title p {
            font-size: 11px;
            color: #444;
            margin: 4px 0 0 0;
        }
    }
</style>
@endsection

@section('content')
<div class="det-container">

{{-- PRINT-ONLY SCHOOL HEADER --}}
<div class="print-school-header" style="display:none;">
    @if($school && $school->logo)
        <img src="{{ asset('storage/' . $school->logo) }}" alt="School Logo">
    @endif
    <div class="psh-info">
        <h2>{{ $school->name ?? 'School Name' }}</h2>
        <p>{{ $school->address ?? '' }}{{ $school->phone ? ' | Ph: ' . $school->phone : '' }}</p>
    </div>
</div>
<div class="print-report-title" style="display:none;">
    <h3>{{ $title }}</h3>
    <p>Date: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }} &nbsp;|&nbsp; Session: {{ $sessionVal }}</p>
</div>

    {{-- BACK BUTTON --}}
    <a href="{{ route('school.reports.index') }}" class="det-back-link">
        <i class="fas fa-arrow-left"></i> Back to All Reports
    </a>

    {{-- HEADER --}}
    <div class="det-hdr">
        <div class="det-hdr-left">
            <h1>{{ $title }}</h1>
            <p>Interactive School Ledger & Collections Reporting</p>
        </div>
        <div class="det-hdr-right no-print">
            <button class="det-btn" onclick="openFiltersModal()"><i class="fas fa-filter" style="color:var(--det-primary);"></i> Filter</button>
            <button class="det-btn det-btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            <button class="det-btn" onclick="printTabularFormat()"><i class="fas fa-receipt"></i> Print 2</button>
            <button class="det-btn det-btn-success" onclick="openExcelModal()"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>

    {{-- FILTERS DISPLAY BAR --}}
    <div class="det-filter-bar">
        <div class="det-filter-info">
            <span>Date Type: <strong class="det-filter-tag">{{ ucwords(str_replace('_', ' ', $dateType)) }}</strong></span>
            <span>Date Range: <strong class="det-filter-tag">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong></span>
            @if($sessionVal)
                <span>Session: <strong class="det-filter-tag">{{ $sessionVal }}</strong></span>
            @endif
        </div>
        <div style="font-size:12px; color:var(--det-text-muted); font-weight:600;">
            * Click header columns to sort table data dynamically
        </div>
    </div>

    {{-- QUICK STAT CARDS --}}
    <div class="det-stats-row">
        <div class="det-stat-card">
            <div class="det-stat-icon" style="background:var(--det-light); color:var(--det-primary);">
                <i class="fas fa-list-ol"></i>
            </div>
            <div>
                <div class="det-stat-label">Total Records</div>
                <div class="det-stat-val" id="statRecordCount">{{ $records->count() }}</div>
            </div>
        </div>
        @foreach($summary as $label => $val)
            <div class="det-stat-card">
                <div class="det-stat-icon" style="background:#d1fae5; color:#059669;">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div>
                    <div class="det-stat-label">{{ $label }}</div>
                    <div class="det-stat-val">{{ $val }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TABLE DATA CARD --}}
    <div class="det-card">
        <div class="det-card-header no-print">
            <div style="font-size:14px; font-weight:800; color:var(--det-text);">
                Report Records Table
            </div>
            <div class="det-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="tableSearchInput" class="det-search-input" placeholder="Search this report..." onkeyup="filterTableData()">
            </div>
        </div>

        <div class="det-table-container">
            <table class="det-table" id="detMainTable">
                <thead>
                    <tr>
                        @foreach($columns as $key => $label)
                            <th onclick="sortTable({{ $loop->index }}, '{{ $key }}')">
                                {{ $label }} <i class="fas fa-sort"></i>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="detTableBody">
                    @forelse($records as $row)
                        <tr>
                            @foreach($columns as $key => $label)
                                <td>{{ $row[$key] ?? '—' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}">
                                <div class="det-empty-state">
                                    <i class="fas fa-search" style="color:#94a3b8;"></i>
                                    <p style="font-weight:700; font-size:14px; margin-top:10px;">No Records Found</p>
                                    <p style="font-size:12px; margin-top:2px;">Try adjusting your filters or date range above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL: FILTERS --}}
<div class="det-modal" id="filtersModal">
    <div class="det-modal-card">
        <div class="det-modal-hdr">
            <h3>Session & Date Filters</h3>
            <button class="det-modal-close" onclick="closeFiltersModal()">&times;</button>
        </div>
        <div class="det-modal-body">
            <form method="GET" action="{{ route('school.reports.detail', $type) }}">
                <div class="det-form-group">
                    <label class="det-form-label">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="det-form-control">
                </div>
                <div class="det-form-group">
                    <label class="det-form-label">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="det-form-control">
                </div>
                <div class="det-form-group">
                    <label class="det-form-label">Academic Session</label>
                    <select name="session" class="det-form-control">
                        <option value="">-- All Sessions --</option>
                        @foreach($sessions as $sess)
                            <option value="{{ $sess->name }}" {{ $sessionVal === $sess->name ? 'selected' : '' }}>{{ $sess->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if(!in_array($type, ['concession_fine_report','discount_report_detailed','estimated_fees','route_wise_transport']))
                <div class="det-form-group">
                    <label class="det-form-label">Filter by Class</label>
                    <select name="class_id" class="det-form-control">
                        <option value="">-- All Classes --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if(in_array($type, ['paid_report','refund_report','dues_report']))
                <div class="det-form-group">
                    <label class="det-form-label">Payment Mode</label>
                    <select name="payment_mode" class="det-form-control">
                        <option value="">-- All Modes --</option>
                        <option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="online" {{ request('payment_mode') === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="cheque" {{ request('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                @endif
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <a href="{{ route('school.reports.detail', $type) }}" class="det-btn">Reset</a>
                    <button type="button" class="det-btn" onclick="closeFiltersModal()">Cancel</button>
                    <button type="submit" class="det-btn det-btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: EXCEL COLUMN EXPORT --}}
<div class="det-modal" id="excelModal">
    <div class="det-modal-card">
        <div class="det-modal-hdr">
            <h3>Choose Columns to Export</h3>
            <button class="det-modal-close" onclick="closeExcelModal()">&times;</button>
        </div>
        <div class="det-modal-body">
            <p style="font-size:12px; color:var(--det-text-muted); margin-bottom:15px;">
                Select columns to include in your generated Excel/CSV workbook. Checked items will be generated.
            </p>
            <div class="det-col-list">
                @foreach($columns as $key => $label)
                    <label class="det-col-item">
                        <input type="checkbox" name="export_cols" value="{{ $key }}" checked>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="det-btn" onclick="closeExcelModal()">Cancel</button>
                <button type="button" class="det-btn det-btn-success" onclick="generateCustomExcel()">Generate Excel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Modal controls
    function openFiltersModal() {
        document.getElementById('filtersModal').classList.add('open');
    }
    function closeFiltersModal() {
        document.getElementById('filtersModal').classList.remove('open');
    }
    function openExcelModal() {
        document.getElementById('excelModal').classList.add('open');
    }
    function closeExcelModal() {
        document.getElementById('excelModal').classList.remove('open');
    }

    // Client-side search filters
    function filterTableData() {
        const query = document.getElementById('tableSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#detTableBody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan > 1) return; // skip empty state row
            
            let match = false;
            for (let i = 0; i < row.cells.length; i++) {
                if (row.cells[i].innerText.toLowerCase().includes(query)) {
                    match = true;
                    break;
                }
            }
            if (match) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('statRecordCount').innerText = visibleCount;
    }

    // Client-side Excel Generator with checked columns
    function generateCustomExcel() {
        const checkboxes = document.querySelectorAll('input[name="export_cols"]:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one column to export.');
            return;
        }

        // Get key mappings and labels
        const colsToExport = [];
        checkboxes.forEach(cb => {
            colsToExport.push({
                key: cb.value,
                label: cb.nextElementSibling.innerText
            });
        });

        // Generate CSV content
        let csvContent = [];
        // Header Row
        csvContent.push(colsToExport.map(col => `"${col.label.replace(/"/g, '""')}"`).join(','));

        // Data Rows
        const dataRows = @json($records);
        dataRows.forEach(row => {
            const rowData = colsToExport.map(col => {
                const val = row[col.key] !== undefined && row[col.key] !== null ? row[col.key].toString() : '';
                return `"${val.replace(/"/g, '""')}"`;
            });
            csvContent.push(rowData.join(','));
        });

        // Trigger file download
        const blob = new Blob([csvContent.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', '{{ strtolower(str_replace(" ", "_", $title)) }}_{{ now()->format("Y-m-d") }}.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        closeExcelModal();
    }

    // Print 2 Format: Fits cleanly and opens standard print window
    function printTabularFormat() {
        const titleText = "{{ $title }}";
        const schoolName = "{{ $school->name ?? 'School' }}";
        const schoolAddress = "{{ $school->address ?? '' }}";
        const schoolPhone = "{{ $school->phone ?? '' }}";
        const dateRangeText = "Date Range: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}";
        const sessionText = "Session: {{ $sessionVal }}";
        
        const printWindow = window.open('', '_blank');
        
        let headerCols = '';
        @foreach($columns as $label)
            headerCols += `<th>{{ $label }}</th>`;
        @endforeach

        let dataRows = '';
        const rawRecords = @json($records);
        rawRecords.forEach(row => {
            dataRows += '<tr>';
            @foreach($columns as $key => $label)
                dataRows += `<td>${row['{{ $key }}'] || '—'}</td>`;
            @endforeach
            dataRows += '</tr>';
        });

        printWindow.document.write(`
            <html>
            <head>
                <title>${schoolName} - ${titleText}</title>
                <style>
                    * { box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 20px; color: #000; font-size: 11px; }
                    .school-header { display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 10px; }
                    .school-header .school-info h2 { margin: 0; font-size: 17px; font-weight: 800; text-transform: uppercase; }
                    .school-header .school-info p { margin: 3px 0 0 0; font-size: 11px; color: #444; }
                    .report-title { text-align: center; margin: 10px 0 14px 0; }
                    .report-title h3 { margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
                    .report-title p { margin: 4px 0 0 0; font-size: 10px; color: #555; }
                    table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
                    th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
                    th { background-color: #f1f5f9; font-weight: bold; }
                    tr:nth-child(even) { background: #f9f9f9; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                <div class="school-header">
                    <div class="school-info">
                        <h2>${schoolName}</h2>
                        <p>${schoolAddress}${schoolPhone ? ' | Ph: ' + schoolPhone : ''}</p>
                    </div>
                </div>
                <div class="report-title">
                    <h3>${titleText}</h3>
                    <p>${dateRangeText} &nbsp;|&nbsp; ${sessionText}</p>
                </div>
                <table>
                    <thead>
                        <tr>${headerCols}</tr>
                    </thead>
                    <tbody>
                        ${dataRows || '<tr><td colspan="{{ count($columns) }}" style="text-align:center;">No Records Found</td></tr>'}
                    </tbody>
                </table>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() { window.close(); }, 500);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    // Client-side sorting logic
    let sortDirections = {};
    function sortTable(colIndex, colKey) {
        const table = document.getElementById("detMainTable");
        const tbody = document.getElementById("detTableBody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        
        if (rows.length === 1 && rows[0].cells[0].colSpan > 1) return; // skip empty state

        // Toggle sort order
        const currentDir = sortDirections[colKey] || 'asc';
        const nextDir = currentDir === 'asc' ? 'desc' : 'asc';
        sortDirections[colKey] = nextDir;

        // Reset all th sort icons
        const headers = table.querySelectorAll("th");
        headers.forEach((th, idx) => {
            const icon = th.querySelector("i");
            if (idx === colIndex) {
                icon.className = nextDir === 'asc' ? "fas fa-sort-up" : "fas fa-sort-down";
                icon.style.color = "var(--det-primary)";
            } else {
                icon.className = "fas fa-sort";
                icon.style.color = "var(--det-text-muted)";
            }
        });

        // Sort rows
        rows.sort((a, b) => {
            let cellA = a.cells[colIndex].innerText.trim();
            let cellB = b.cells[colIndex].innerText.trim();

            // Strip currency symbols for numeric sorting
            const cleanA = cellA.replace(/[₹\s,]/g, '');
            const cleanB = cellB.replace(/[₹\s,]/g, '');

            if (!isNaN(cleanA) && !isNaN(cleanB)) {
                return nextDir === 'asc' ? Number(cleanA) - Number(cleanB) : Number(cleanB) - Number(cleanA);
            }

            return nextDir === 'asc' 
                ? cellA.localeCompare(cellB, undefined, { numeric: true, sensitivity: 'base' })
                : cellB.localeCompare(cellA, undefined, { numeric: true, sensitivity: 'base' });
        });

        // Re-append sorted rows
        tbody.innerHTML = "";
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endsection
