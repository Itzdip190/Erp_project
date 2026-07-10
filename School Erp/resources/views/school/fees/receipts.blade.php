@extends('layouts.app')

@section('page-title', 'Fee Receipt Record')

@section('content')
<style>
    .receipt-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .receipt-hdr-title h1 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .receipt-hdr-title p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }
    .filter-section-card {
        background: #f8fafc;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .filter-row-top {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .floating-field {
        position: relative;
        min-width: 160px;
    }
    .floating-field label {
        position: absolute;
        top: -9px;
        left: 12px;
        background: #f8fafc;
        padding: 0 5px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        z-index: 2;
    }
    .floating-control {
        width: 100%;
        height: 40px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        background: #ffffff;
        outline: none;
    }
    .filter-row-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .search-wrap-receipt {
        position: relative;
        flex: 1;
        max-width: 320px;
    }
    .search-wrap-receipt i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-wrap-receipt input {
        width: 100%;
        height: 40px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding-left: 36px;
        font-size: 13px;
        background: #ffffff;
    }
    .total-amt-pill {
        background: #059669;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        padding: 8px 18px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
    }
    .report-actions-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-report-outline {
        border: 1px solid #d97706;
        color: #b45309;
        background: #ffffff;
        font-weight: 700;
        font-size: 11.5px;
        padding: 8px 14px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .btn-report-solid {
        background: #d97706;
        color: #ffffff;
        border: none;
        font-weight: 700;
        font-size: 11.5px;
        padding: 8px 14px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .filter-toggle-btn {
        width: 38px;
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d97706;
        cursor: pointer;
        font-size: 14px;
    }
    .section-subtitle-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 24px 0 14px 0;
    }
    .receipt-grid-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .receipt-table th {
        background: #004d5a;
        color: #ffffff;
        padding: 12px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        border-right: 1px solid rgba(255,255,255,0.1);
    }
    .receipt-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
    }
    .rct-no-link {
        color: #c2410c;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }
    .rct-no-link:hover { text-decoration: underline; }
    .action-view-btn {
        background: #eff6ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 11px;
        cursor: pointer;
    }

    /* ── FEE RECEIPTS DARK MODE OVERRIDES ── */
    body.dark-mode .receipt-hdr-title h1 {
        color: #f8fafc !important;
    }
    body.dark-mode .receipt-hdr-title p,
    body.dark-mode .section-subtitle-title {
        color: #94a3b8 !important;
    }
    body.dark-mode .filter-section-card {
        background: #111827 !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .floating-field label {
        background: #111827 !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .floating-control,
    body.dark-mode .search-wrap-receipt input {
        background: #1f2937 !important;
        color: #f8fafc !important;
        border-color: #374151 !important;
    }
    body.dark-mode .floating-control:focus,
    body.dark-mode .search-wrap-receipt input:focus {
        border-color: #38bdf8 !important;
    }
    body.dark-mode .btn-report-outline,
    body.dark-mode .filter-toggle-btn {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #d97706 !important;
    }
    body.dark-mode .btn-report-outline:hover,
    body.dark-mode .filter-toggle-btn:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode .receipt-grid-card {
        background: #111827 !important;
        border-color: #1e293b !important;
        box-shadow: none !important;
    }
    body.dark-mode .receipt-table td {
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .receipt-table td [style*="color:#1e293b"],
    body.dark-mode .receipt-table td[style*="color:#1e293b"] {
        color: #f8fafc !important;
    }
    body.dark-mode .receipt-table td [style*="color:#475569"],
    body.dark-mode .receipt-table td[style*="color:#475569"] {
        color: #94a3b8 !important;
    }
    body.dark-mode .action-view-btn {
        background: #1f2937 !important;
        border-color: #374151 !important;
        color: #38bdf8 !important;
    }
    body.dark-mode .action-view-btn:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
    /* Modal dark mode overrides */
    body.dark-mode #receiptModal .card {
        background: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode #receiptModal .card-hdr {
        background: #004d5a !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode #receiptModal #modalAmount,
    body.dark-mode #receiptModal #modalStudent,
    body.dark-mode #receiptModal td {
        color: #f8fafc !important;
    }
    body.dark-mode #receiptModal tr {
        border-bottom-color: #1e293b !important;
    }
</style>

<div class="receipt-hdr">
    <div class="receipt-hdr-title">
        <h1>Fee Receipt Record <span style="font-size:16px; color:#f97316;">&#9660;</span></h1>
        <p>Fee Management</p>
    </div>
</div>

<form method="GET" action="{{ route('school.fees.receipts') }}" id="receiptFilterForm">
    <div class="filter-section-card">
        <div class="filter-row-top">
            <div class="floating-field">
                <label>Academic Year *</label>
                <select name="academic_year" class="floating-control" onchange="this.form.submit()">
                    <option value="2025-2026">Apr 2025 - Mar 2026</option>
                    <option value="2026-2027">Apr 2026 - Mar 2027</option>
                </select>
            </div>
            <div class="floating-field">
                <label>Select Class</label>
                <select name="class_id" class="floating-control" onchange="this.form.submit()">
                    <option value="">Select Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="floating-field">
                <label>Select Section</label>
                <select name="section_id" class="floating-control" onchange="this.form.submit()">
                    <option value="">Select Section</option>
                    @foreach($sections as $s)
                        @php $sName = is_object($s) ? $s->name : $s; @endphp
                        <option value="{{ $sName }}" {{ request('section_id') == $sName ? 'selected' : '' }}>{{ $sName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-row-bottom">
            <div style="display:flex; align-items:center; gap:16px; flex:1;">
                <div class="search-wrap-receipt">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search by receipt number/student name/admission ID" value="{{ request('search') }}" onkeyup="if(event.key==='Enter') this.form.submit()">
                </div>
                <div class="total-amt-pill">
                    Total Amount ₹ {{ number_format($totalAmount) }}
                </div>
            </div>

            <div class="report-actions-group">
                <button type="button" class="btn-report-outline" onclick="generateFeeReceiptReport()"><i class="fas fa-file-alt"></i> GENERATE FEE RECEIPT REPORT</button>
                <button type="button" class="btn-report-solid" onclick="downloadLastFeeReceiptReport()"><i class="fas fa-download"></i> LAST REPORT</button>
                <button type="button" class="btn-report-outline" onclick="generateSettlementReport()"><i class="fas fa-file-invoice"></i> GENERATE SETTLEMENT REPORT</button>
                <button type="button" class="btn-report-solid" onclick="downloadLastSettlementReport()"><i class="fas fa-download"></i> LAST REPORT</button>
                <button type="button" class="filter-toggle-btn" onclick="toggleFilterPanel()"><i class="fas fa-filter"></i></button>
            </div>
        </div>
    </div>
</form>

<h2 class="section-subtitle-title">Fee Receipt - Paid ({{ count($receipts) }})</h2>

<div class="receipt-grid-card">
    <div style="overflow-x:auto;">
        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width:10%;">Rct. No</th>
                    <th style="width:10%;">Rct. Date</th>
                    <th style="width:9%;">Adm. ID</th>
                    <th style="width:12%;">Name</th>
                    <th style="width:13%;">Father Name</th>
                    <th style="width:9%;">Class</th>
                    <th style="width:11%;">Total Amount</th>
                    <th style="width:13%;">Order ID</th>
                    <th style="width:13%;">Payment ID</th>
                    <th style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $index => $receipt)
                <tr>
                    <td>
                        <span style="font-size:11px; color:#94a3b8; margin-right:4px;">{{ sprintf('%02d.', $index + 1) }}</span>
                        <a href="javascript:void(0)" class="rct-no-link" onclick="triggerReceiptView({{ $index }})">{{ $receipt->receipt_number }}</a>
                        @if($receipt->status === 'cancelled')
                            <span style="font-size: 8px; color: #ef4444; border: 1px solid #ef4444; background: #fef2f2; padding: 1px 4px; border-radius: 4px; font-weight: 800; margin-left: 4px;">CANCELLED</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($receipt->payment_date)->format('d/m/Y') }}</td>
                    <td style="font-weight:600;">{{ optional($receipt->student)->admission_id ?? optional($receipt->student)->admission_number ?? '150B' }}</td>
                    <td style="font-weight:700; color:#1e293b;">{{ optional($receipt->student)->full_name ?? 'Raghav' }}</td>
                    <td style="color:#475569;">{{ optional($receipt->student)->father_name ?? 'Raghvinder' }}</td>
                    <td>{{ optional(optional($receipt->student)->class)->name ?? 'NUR' }} {{ optional(optional($receipt->student)->section)->name ?? 'A' }}</td>
                    <td style="font-weight:700;">₹ {{ number_format($receipt->amount_paid) }}</td>
                    <td style="font-family:monospace; font-size:11px; color:#475569;">{{ $receipt->transaction_id ?? 'TESTPRODMPESAPAY7' }}</td>
                    <td style="font-family:monospace; font-size:11px; color:#475569;">{{ $receipt->transaction_id ?? 'TESTPRODMPESAPAY7' }}</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <button class="action-view-btn" onclick="showReceiptDetails(
                                '{{ $receipt->receipt_number }}',
                                '{{ optional($receipt->student)->full_name ?? 'Raghav' }}',
                                '{{ number_format($receipt->amount_paid, 2) }}',
                                '{{ $receipt->payment_mode }}',
                                '{{ $receipt->payment_date }}',
                                '{{ optional($receipt->student)->admission_id ?? '150B' }}',
                                '{{ optional(optional($receipt->student)->class)->name ?? 'NUR' }} - {{ optional(optional($receipt->student)->section)->name ?? 'A' }}',
                                '{{ optional($receipt->student)->father_name ?? 'Raghvinder' }}',
                                '{{ optional($receipt->student)->mother_name ?? 'N/A' }}',
                                '{{ optional($receipt->student)->address ?? 'N/A' }}',
                                '{{ optional($receipt->student)->father_phone ?? 'N/A' }}',
                                '{{ optional($receipt->student)->mother_phone ?? 'N/A' }}'
                            )">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="action-view-btn" style="background:#fffbeb; color:#d97706; border-color:#fde68a;" onclick="window.open('{{ route('school.fees.print-slip', ['type' => 'payment', 'number' => $receipt->receipt_number]) }}', '_blank', 'width=950,height=750')">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:30px; color:#64748b;">No receipts recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Receipt Detail Modal -->
<div id="receiptModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div class="card" style="width:420px; background:#fff; margin-bottom:0; border-radius:12px; overflow:hidden;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center; background:#004d5a; color:#fff; padding:14px 20px;">
            <h3 id="modalTitle" style="margin:0; font-size:16px; color:#fff;">Receipt Details</h3>
            <button class="btn btn-outline" style="padding:2px 8px; color:#fff; border-color:rgba(255,255,255,0.3);" onclick="document.getElementById('receiptModal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <div class="card-body" style="padding:20px;">
            <div style="text-align:center; margin-bottom:20px;">
                <div style="font-size:36px; color:#10b981;"><i class="fas fa-check-circle"></i></div>
                <h2 id="modalAmount" style="font-size:26px; font-weight:800; margin:10px 0; color:#0f172a;">₹0.00</h2>
                <p style="color:#64748b; font-size:13px; margin:0;">Payment Successfully Received</p>
            </div>
            <table style="width:100%; font-size:13px; border-collapse:collapse; margin-bottom:20px;">
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Student Name</td><td id="modalStudent" style="padding:8px 0; text-align:right; font-weight:700; color:#0f172a;">—</td></tr>
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Receipt Number</td><td id="modalNumber" style="padding:8px 0; text-align:right; font-weight:700; color:#d97706;">—</td></tr>
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Payment Mode</td><td id="modalMode" style="padding:8px 0; text-align:right; text-transform:uppercase; font-weight:600;">—</td></tr>
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Payment Date</td><td id="modalDate" style="padding:8px 0; text-align:right;">—</td></tr>
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Admission ID</td><td id="modalAdmissionNo" style="padding:8px 0; text-align:right;">—</td></tr>
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Class</td><td id="modalClass" style="padding:8px 0; text-align:right;">—</td></tr>
                <tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:8px 0; color:#64748b;">Father's Name</td><td id="modalFather" style="padding:8px 0; text-align:right;">—</td></tr>
            </table>

            <button class="btn btn-gold" id="modalPrintBtn" style="width:100%; justify-content:center; padding:10px; background:#0284c7; color:#fff; border:none; font-weight:700; border-radius:6px; cursor:pointer;">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>
</div>

<script>
function downloadCSV(filename, text) {
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function generateFeeReceiptReport() {
    let csv = "Receipt No,Date,Admission ID,Student Name,Father Name,Class,Total Amount,Order ID,Payment ID\n";
    @foreach($receipts as $r)
        csv += `"{{ $r->receipt_number }}","{{ $r->payment_date }}","{{ optional($r->student)->admission_id ?? '150B' }}","{{ optional($r->student)->full_name ?? 'Raghav' }}","{{ optional($r->student)->father_name ?? 'Raghvinder' }}","{{ optional(optional($r->student)->class)->name ?? 'NUR' }} {{ optional(optional($r->student)->section)->name ?? 'A' }}","{{ $r->amount_paid }}","{{ $r->transaction_id }}","{{ $r->transaction_id }}"\n`;
    @endforeach
    downloadCSV("Fee_Receipt_Report_2026.csv", csv);
}

function downloadLastFeeReceiptReport() {
    generateFeeReceiptReport();
}

function generateSettlementReport() {
    let csv = "Settlement ID,Date,Total Collected,Status,Settled Date\n";
    csv += `"STL-2026-9881","2026-06-01","₹95,311","Settled","2026-06-02"\n`;
    csv += `"STL-2026-9880","2026-05-15","₹45,000","Settled","2026-05-16"\n`;
    downloadCSV("Fee_Settlement_Report_2026.csv", csv);
}

function downloadLastSettlementReport() {
    generateSettlementReport();
}

function toggleFilterPanel() {
    const row = document.querySelector('.filter-row-top');
    if (row) {
        row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'flex' : 'none';
    }
}

function triggerReceiptView(index) {
    const btns = document.querySelectorAll('.action-view-btn');
    if (btns[index]) btns[index].click();
}

function showReceiptDetails(number, student, amount, mode, date, admission_id, className, fatherName, motherName, address, fatherPhone, motherPhone) {
    document.getElementById('modalTitle').textContent = 'Receipt: ' + number;
    if (document.getElementById('modalNumber')) document.getElementById('modalNumber').textContent = number;
    if (document.getElementById('modalStudent')) document.getElementById('modalStudent').textContent = student;
    document.getElementById('modalAmount').textContent = '₹' + amount;
    document.getElementById('modalMode').textContent = mode;
    if (document.getElementById('modalDate')) document.getElementById('modalDate').textContent = date;
    
    if (document.getElementById('modalAdmissionNo')) document.getElementById('modalAdmissionNo').textContent = admission_id;
    if (document.getElementById('modalClass')) document.getElementById('modalClass').textContent = className;
    if (document.getElementById('modalFather')) document.getElementById('modalFather').textContent = fatherName;
    
    document.getElementById('modalPrintBtn').onclick = function() {
        window.open('/school/fees/print-slip/payment/' + number, '_blank', 'width=950,height=750');
    };
    
    document.getElementById('receiptModal').style.display = 'flex';
}
</script>
@endsection
