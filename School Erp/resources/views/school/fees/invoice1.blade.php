@extends('layouts.app')

@section('title', 'Fee Invoice 1')
@section('page-title', 'Fee Invoice 1')

@section('styles')
<style>
    /* Main container styling in Blue and White */
    .invoice1-container {
        font-family: 'Inter', sans-serif;
        background: #f8fafc;
        padding: 4px;
        color: #0f172a;
    }

    /* Filter Grid Area */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .filter-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .filter-label {
        position: absolute;
        top: -8px;
        left: 10px;
        background: #f8fafc; /* matches content container background */
        padding: 0 5px;
        font-size: 10px;
        font-weight: 800;
        color: #2563eb;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .filter-card i.filter-icon {
        font-size: 14px;
        color: #3b82f6;
        flex-shrink: 0;
    }
    .filter-card select {
        flex-grow: 1;
        border: none;
        background: transparent;
        outline: none;
        font-size: 13px;
        color: #0f172a;
        font-weight: 600;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        padding-right: 15px;
        width: 100%;
    }
    .filter-card::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 9px;
        color: #3b82f6;
        position: absolute;
        right: 12px;
        pointer-events: none;
    }

    /* Table Container & Utilities */
    .table-container-card {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.04);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .table-utility-bar {
        background: #1e3a8a; /* Deep Blue */
        padding: 12px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }
    .utility-left-actions {
        display: flex;
        gap: 18px;
    }
    .utility-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        transition: color 0.2s;
    }
    .utility-btn:hover {
        color: #93c5fd;
    }
    .utility-btn i {
        font-size: 12px;
    }

    /* Table styles */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    table.fee-datagrid {
        width: 100%;
        border-collapse: collapse;
    }
    table.fee-datagrid th {
        background: #1e3a8a; /* Deep Blue */
        color: #ffffff;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        text-align: left;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }
    table.fee-datagrid td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #0f172a;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        vertical-align: middle;
    }
    table.fee-datagrid tr:hover td {
        background: #f1f5f9;
    }

    /* Serial number styling */
    .row-index {
        color: #64748b;
        font-size: 11px;
        margin-right: 10px;
        font-weight: 600;
        display: inline-block;
        min-width: 20px;
    }
    .admission-id-text {
        font-weight: 800;
        color: #1e40af;
    }

    /* Amount styling */
    .amount-text {
        font-weight: 800;
        color: #0f172a;
    }

    /* Invoice No tag in Blue theme */
    .invoice-tag {
        display: inline-flex;
        align-items: center;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 800;
        font-size: 11.5px;
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        transition: all 0.2s;
    }
    .invoice-tag:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
    .no-invoice-tag {
        color: #94a3b8;
        font-style: italic;
        font-size: 12px;
    }

    /* Pagination area */
    .table-footer {
        padding: 14px 20px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .total-rows-badge {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        font-weight: 800;
        padding: 6px 16px;
        border-radius: 20px;
    }

    .custom-pagination {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pagination-btn {
        width: 34px;
        height: 34px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .pagination-btn:hover:not(.disabled):not(.active) {
        background: #eff6ff;
        border-color: #2563eb;
        color: #2563eb;
    }
    .pagination-btn.active {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
    }
    .pagination-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* General loading indicator */
    .loading-overlay {
        position: relative;
    }
    .loading-spinner {
        display: none;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        z-index: 10;
        align-items: center;
        justify-content: center;
    }
    .spinner {
        width: 42px;
        height: 42px;
        border: 4px solid #bfdbfe;
        border-top-color: #1e3a8a;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Checkbox styling */
    .checkbox-col {
        width: 40px;
        text-align: center !important;
    }
    .custom-checkbox {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #2563eb;
    }

    /* Custom search box above table */
    .search-row {
        background: #ffffff;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-input-wrap {
        position: relative;
        width: 280px;
    }
    .search-input-wrap input {
        width: 100%;
        padding: 8px 14px 8px 36px;
        font-size: 13px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        outline: none;
        color: #0f172a;
        font-weight: 500;
    }
    .search-input-wrap input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .search-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #3b82f6;
        font-size: 13px;
    }

    /* Floating success/error notification toast */
    .custom-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        transform: translateY(100px);
        opacity: 0;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s;
    }
    .custom-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    .custom-toast.success {
        border-left: 4px solid #2563eb;
    }
    .custom-toast.error {
        border-left: 4px solid #ef4444;
    }

    /* ── FEE INVOICE 1 DARK MODE OVERRIDES ── */
    body.dark-mode .invoice1-container {
        background: #0f172a !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .page-hdr-left h1 {
        color: #f8fafc !important;
    }
    body.dark-mode .page-hdr-left p {
        color: #94a3b8 !important;
    }
    body.dark-mode .filter-card,
    body.dark-mode .table-container-card,
    body.dark-mode .table-footer,
    body.dark-mode .search-row,
    body.dark-mode table.fee-datagrid td {
        background: #111827 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .filter-label {
        background: #0f172a !important;
        color: #818cf8 !important;
    }
    body.dark-mode .filter-card select {
        color: #f8fafc !important;
    }
    body.dark-mode .search-input-wrap input {
        background: #1f2937 !important;
        color: #f8fafc !important;
        border-color: #374151 !important;
    }
    body.dark-mode .search-input-wrap input:focus {
        border-color: #38bdf8 !important;
    }
    body.dark-mode table.fee-datagrid tr:hover td {
        background: rgba(255, 255, 255, 0.04) !important;
    }
    body.dark-mode .amount-text {
        color: #f8fafc !important;
    }
    body.dark-mode .admission-id-text {
        color: #60a5fa !important;
    }
    body.dark-mode .invoice-tag {
        background: rgba(37, 99, 235, 0.15) !important;
        color: #60a5fa !important;
        border-color: rgba(37, 99, 235, 0.3) !important;
    }
    body.dark-mode .invoice-tag:hover {
        background: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
    }
    body.dark-mode .total-rows-badge {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #60a5fa !important;
    }
    body.dark-mode .pagination-btn {
        background: #1f2937 !important;
        color: #cbd5e1 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .pagination-btn:hover:not(.disabled):not(.active) {
        background: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode .pagination-btn.active {
        background: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice" style="color:var(--gold);margin-right:8px;"></i>Fee Invoice 1</h1>
        <p>Manage and generate installment fee invoices for students</p>
    </div>
</div>

<div class="invoice1-container">

    <!-- Filters Row -->
    <div class="filter-grid">
        <!-- Academic Year -->
        <div class="filter-card">
            <label class="filter-label">Academic Year *</label>
            <i class="filter-icon far fa-calendar-days"></i>
            <select id="filter_academic_year">
                @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ $session->name == 'Apr 2025 - Mar 2026' ? 'selected' : '' }}>
                        {{ $session->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Select Class -->
        <div class="filter-card">
            <label class="filter-label">Select Class</label>
            <i class="filter-icon far fa-file-lines"></i>
            <select id="filter_class">
                <option value="">Select Class</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Select Section -->
        <div class="filter-card">
            <label class="filter-label">Select Section</label>
            <i class="filter-icon far fa-file-lines"></i>
            <select id="filter_section">
                <option value="">Select Section</option>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}" data-class-id="{{ $s->class_id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Select Fee Schedule -->
        <div class="filter-card">
            <label class="filter-label">Select Fee Schedule</label>
            <i class="filter-icon far fa-file-lines"></i>
            <select id="filter_schedule">
                <option value="">Select Fee Schedule</option>
                @foreach($feeSchedules as $fs)
                    <option value="{{ $fs->id }}">{{ $fs->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Select Installments -->
        <div class="filter-card">
            <label class="filter-label">Select installments</label>
            <i class="filter-icon far fa-file-lines"></i>
            <select id="filter_installment">
                <option value="">Select installments</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">Installment {{ $i }}</option>
                @endfor
            </select>
        </div>

        <!-- Select Component(s) -->
        <div class="filter-card">
            <label class="filter-label">Select Component(s)</label>
            <i class="filter-icon far fa-file-lines"></i>
            <select id="filter_component">
                <option value="">Select Component(s)</option>
                @foreach($feeComponents as $fc)
                    <option value="{{ $fc->id }}">{{ $fc->component_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Select Route -->
        <div class="filter-card">
            <label class="filter-label">Select Route</label>
            <i class="filter-icon fas fa-route"></i>
            <select id="filter_route">
                <option value="">Select Route</option>
                @foreach($routes as $rt)
                    <option value="{{ $rt->name }}">{{ $rt->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-container-card loading-overlay">
        <!-- Spinner Overlay -->
        <div class="loading-spinner" id="tableSpinner">
            <div class="spinner"></div>
        </div>

        <!-- Utility Bar -->
        <div class="table-utility-bar">
            <div class="utility-left-actions">
                <button class="utility-btn">
                    <i class="fas fa-columns"></i> COLUMNS
                </button>
                <button class="utility-btn">
                    <i class="fas fa-filter"></i> FILTERS
                </button>
                <button class="utility-btn">
                    <i class="fas fa-align-left"></i> DENSITY
                </button>
                <button class="utility-btn">
                    <i class="fas fa-download"></i> EXPORT
                </button>
            </div>
            
            <div class="utility-right-actions">
                <button class="btn" id="btnBulkInvoice" onclick="generateSelectedInvoices()" style="display: none; padding: 6px 14px; font-size: 12px; font-weight: 800; background: #2563eb; color: #ffffff; border-radius: 6px; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(37,99,235,0.3);">
                    <i class="fas fa-file-invoice" style="margin-right:4px;"></i> Generate Invoice(s)
                </button>
            </div>
        </div>

        <!-- Search row -->
        <div class="search-row">
            <div class="search-input-wrap">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" id="grid_search" placeholder="Search by student, admission ID...">
            </div>
            <div style="font-size: 12.5px; color: #475569; font-weight: 600;">
                Select rows to generate invoices.
            </div>
        </div>

        <!-- Datagrid Table -->
        <div class="table-responsive">
            <table class="fee-datagrid">
                <thead>
                    <tr>
                        <th class="checkbox-col">
                            <input type="checkbox" class="custom-checkbox" id="check_all_rows">
                        </th>
                        <th>Admission ID</th>
                        <th>Student Name</th>
                        <th>Father's Name</th>
                        <th>Class</th>
                        <th>Fee Schedule</th>
                        <th>Installment</th>
                        <th>Component</th>
                        <th>Amount</th>
                        <th>Fee Invoice No.</th>
                    </tr>
                </thead>
                <tbody id="grid_tbody">
                    <!-- Dynamic Rows Loaded by AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="table-footer">
            <div class="total-rows-badge" id="total_rows_badge">
                Total Rows: 0
            </div>
            
            <div class="custom-pagination" id="grid_pagination">
                <!-- Pagination buttons generated dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Floating Custom Toast Notification -->
<div class="custom-toast" id="toastBox">
    <i class="fas fa-circle-check" id="toastIcon" style="font-size: 16px;"></i>
    <span id="toastMessage">Success message here</span>
</div>

<!-- Invoice Print Modal -->
<div class="modal-overlay" id="invoiceModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.6); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(6px); transition: all 0.3s ease;">
    <div style="background:#ffffff; width:90%; max-width:850px; height:85%; border-radius:16px; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #e2e8f0; animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="padding:18px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-family:'Plus Jakarta Sans',sans-serif; color:#0f172a; font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px;"><i class="fas fa-file-invoice" style="color:#2563eb;"></i> Invoice Print Preview</h3>
            <button onclick="closeInvoiceModal()" style="background:#f1f5f9; border:none; color:#64748b; font-size:14px; width:28px; height:28px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#ef4444'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'"><i class="fas fa-xmark"></i></button>
        </div>
        <div style="flex:1; background:#f1f5f9; position:relative; padding: 2px;">
            <iframe id="invoiceIframe" src="" style="width:100%; height:100%; border:none; background:#ffffff; border-radius: 0 0 12px 12px;"></iframe>
        </div>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let totalPages = 1;

    $(document).ready(function() {
        // Load initial grid data
        loadGridData();

        // Listen for filter changes
        $('#filter_academic_year, #filter_class, #filter_section, #filter_schedule, #filter_installment, #filter_component, #filter_route').change(function() {
            currentPage = 1;
            loadGridData();
        });

        // Dynamic Class-Section dropdown filtering and deduplication
        function filterSectionOptions() {
            let classId = $('#filter_class').val();
            let seenNames = new Set();
            
            $('#filter_section option').each(function() {
                let val = $(this).val();
                if (!val) {
                    $(this).show();
                    return;
                }
                
                let sectionClassId = $(this).data('class-id');
                let sectionName = $(this).data('name') || $(this).text().trim();
                
                if (classId) {
                    if (sectionClassId == classId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } else {
                    if (!seenNames.has(sectionName)) {
                        seenNames.add(sectionName);
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            });
        }

        // Initialize section dropdown on ready
        filterSectionOptions();

        $('#filter_class').change(function() {
            filterSectionOptions();
            $('#filter_section').val('');
        });

        // Search text filtering with debounce
        let searchTimeout = null;
        $('#grid_search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentPage = 1;
                loadGridData();
            }, 300);
        });

        // Master check_all_rows handler
        $('#check_all_rows').change(function() {
            let isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
            toggleBulkButton();
        });

        // Row checkboxes state handler
        $(document).on('change', '.row-checkbox', function() {
            let totalCheckboxes = $('.row-checkbox').length;
            let checkedCheckboxes = $('.row-checkbox:checked').length;
            
            $('#check_all_rows').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
            toggleBulkButton();
        });
    });

    // Helper to toggle "Generate Invoice(s)" bulk button
    function toggleBulkButton() {
        let checkedCount = $('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#btnBulkInvoice').html(`<i class="fas fa-file-invoice" style="margin-right:4px;"></i> Generate Invoice(s) (${checkedCount})`).show();
        } else {
            $('#btnBulkInvoice').hide();
        }
    }

    // Main AJAX function to load grid data
    function loadGridData(page = 1) {
        $('#tableSpinner').css('display', 'flex');
        currentPage = page;

        // Reset check all & hide bulk button on page change
        $('#check_all_rows').prop('checked', false);
        $('#btnBulkInvoice').hide();

        let filters = {
            ajax: 1,
            page: page,
            academic_session_id: $('#filter_academic_year').val(),
            class_id: $('#filter_class').val(),
            section_id: $('#filter_section').val(),
            transport_route: $('#filter_route').val(),
            fee_schedule_id: $('#filter_schedule').val(),
            installment_no: $('#filter_installment').val(),
            fee_component_id: $('#filter_component').val(),
            search: $('#grid_search').val()
        };

        $.ajax({
            url: "{{ route('school.fees.invoice1') }}",
            type: "GET",
            data: filters,
            dataType: "json",
            success: function(response) {
                $('#tableSpinner').hide();
                renderTable(response.data, response.from || 1);
                renderPagination(response);
                $('#total_rows_badge').text('Total Rows: ' + response.total);
            },
            error: function(xhr, status, error) {
                $('#tableSpinner').hide();
                console.error("AJAX Error: ", error);
                showNotification("Failed to fetch fee data.", "error");
            }
        });
    }

    // Function to render table records
    function renderTable(data, startFrom) {
        let html = '';
        if (data.length === 0) {
            html = `<tr>
                <td colspan="10" style="text-align: center; padding: 40px; color: #64748b;">
                    <i class="fas fa-box-open" style="font-size: 36px; display: block; margin-bottom: 12px; color: #94a3b8;"></i>
                    No fee dues found matching the selected filters.
                </td>
            </tr>`;
        } else {
            data.forEach((row, index) => {
                let formattedIdx = String(startFrom + index).padStart(2, '0');
                let fatherName = row.father_name || '';
                let className = row.class_name ? `${row.class_name} - ${row.section_name || ''}` : '';
                let scheduleName = row.schedule_name || '';
                
                // Capitalize installment strings/numbers nicely
                let installment = row.installment_no;
                if (row.component_name && row.component_name.toLowerCase().includes('transport')) {
                    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    let mIndex = parseInt(installment) - 1;
                    if (mIndex >= 0 && mIndex < 12) {
                        installment = months[mIndex];
                    } else {
                        installment = `Month ${installment}`;
                    }
                } else {
                    if (!isNaN(installment)) {
                        installment = `Installment ${installment}`;
                    } else if (installment) {
                        installment = installment.charAt(0).toUpperCase() + installment.slice(1);
                    } else {
                        installment = 'N/A';
                    }
                }

                let invoiceTag = row.invoice_no 
                    ? `<a href="javascript:void(0);" onclick="openInvoiceModal('${row.invoice_no}', '${row.student_id}')" class="invoice-tag" style="text-decoration:none;"><i class="fas fa-print" style="margin-right:4px;"></i> ${row.invoice_no}</a>`
                    : `<span class="no-invoice-tag">—</span>`;

                // If invoice already exists, disable checking this row to prevent generating twice
                let checkboxInput = row.invoice_no
                    ? `<input type="checkbox" class="custom-checkbox row-checkbox" value="${row.fee_id}" disabled>`
                    : `<input type="checkbox" class="custom-checkbox row-checkbox" value="${row.fee_id}">`;

                html += `<tr>
                    <td class="checkbox-col">${checkboxInput}</td>
                    <td>
                        <span class="row-index">${formattedIdx}.</span>
                        <span class="admission-id-text">${row.admission_id}</span>
                    </td>
                    <td style="font-weight:600;">
                        <div>${row.student_name}</div>
                        ${row.transport_route ? `<div style="font-size:11px; color:#2563eb; font-weight:700; margin-top:3px;"><i class="fas fa-route"></i> ${row.transport_route}</div>` : ''}
                    </td>
                    <td>${fatherName}</td>
                    <td>${className}</td>
                    <td>${scheduleName}</td>
                    <td>${installment}</td>
                    <td>${row.component_name || 'Late Fine'}</td>
                    <td class="amount-text">₹ ${Number(row.amount).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 2})}</td>
                    <td>${invoiceTag}</td>
                </tr>`;
            });
        }
        $('#grid_tbody').html(html);
    }

    // Function to render pagination buttons
    function renderPagination(response) {
        totalPages = response.last_page;
        let html = '';
        
        // Prev button
        let prevClass = response.current_page === 1 ? 'disabled' : '';
        html += `<button class="pagination-btn ${prevClass}" onclick="changePage(${response.current_page - 1})"><i class="fas fa-chevron-left"></i></button>`;

        // Numbered buttons
        let startPage = Math.max(1, response.current_page - 2);
        let endPage = Math.min(totalPages, response.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            let activeClass = i === response.current_page ? 'active' : '';
            html += `<button class="pagination-btn ${activeClass}" onclick="changePage(${i})">${i}</button>`;
        }

        // Next button
        let nextClass = response.current_page === totalPages ? 'disabled' : '';
        html += `<button class="pagination-btn ${nextClass}" onclick="changePage(${response.current_page + 1})"><i class="fas fa-chevron-right"></i></button>`;

        $('#grid_pagination').html(html);
    }

    function changePage(page) {
        if (page < 1 || page > totalPages) return;
        loadGridData(page);
    }

    // Function to call AJAX generation endpoint
    function generateSelectedInvoices() {
        let selectedFeeIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedFeeIds.push($(this).val());
        });

        if (selectedFeeIds.length === 0) {
            showNotification("No records selected.", "error");
            return;
        }

        $('#tableSpinner').css('display', 'flex');

        $.ajax({
            url: "{{ route('school.fees.invoice1.generate') }}",
            type: "POST",
            data: {
                fee_ids: selectedFeeIds
            },
            dataType: "json",
            success: function(response) {
                $('#tableSpinner').hide();
                if (response.success) {
                    showNotification(response.message, "success");
                    loadGridData(currentPage);
                } else {
                    showNotification(response.message || "Failed to generate invoices.", "error");
                }
            },
            error: function(xhr, status, error) {
                $('#tableSpinner').hide();
                let errMsg = "An error occurred while generating invoices.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                showNotification(errMsg, "error");
                console.error("AJAX Error: ", error);
            }
        });
    }

    // Dynamic floating notification toast builder
    function showNotification(msg, type = "success") {
        const toast = $('#toastBox');
        const icon = $('#toastIcon');
        
        $('#toastMessage').text(msg);
        
        toast.removeClass('success error show');
        icon.removeClass('fa-circle-check fa-circle-exclamation');

        if (type === "success") {
            toast.addClass('success');
            icon.addClass('fa-circle-check').css('color', '#2563eb');
        } else {
            toast.addClass('error');
            icon.addClass('fa-circle-exclamation').css('color', '#ef4444');
        }

        toast.addClass('show');

        // Automatically hide after 4 seconds
        setTimeout(function() {
            toast.removeClass('show');
        }, 4000);
    }

    function openInvoiceModal(invoiceNo, studentId) {
        let printUrl = `/school/fees/print-slip/invoice/${invoiceNo}?student_id=${studentId}`;
        $('#invoiceIframe').attr('src', printUrl);
        $('#invoiceModal').css('display', 'flex');
    }

    function closeInvoiceModal() {
        $('#invoiceModal').hide();
        $('#invoiceIframe').attr('src', '');
    }
</script>
@endsection
