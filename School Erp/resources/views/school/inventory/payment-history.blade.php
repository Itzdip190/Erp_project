@extends('layouts.app')

@section('page-title', 'Payment History - Inventory Management')

@section('content')
<style>
    /* ─── Standard ERP Royal Blue & White Theme (Matching Image 2 Specs) ─── */
    :root {
        --erp-blue-dark:   #1e3a8a;
        --erp-blue:        #2563eb;
        --erp-blue-light:  #3b82f6;
        --erp-blue-soft:   #eff6ff;
        --erp-blue-border: #dbeafe;
        --erp-card-bg:     #ffffff;
        --erp-border:      #e2e8f0;
        --erp-text-dark:   #0f172a;
        --erp-text-muted:  #64748b;
        --erp-paid-green:  #16a34a;
        --erp-paid-bg:     #f0fdf4;
    }

    /* ─── Full Screen Responsive Container ─────────────────────────────── */
    .inv-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 16px 20px 40px !important;
        box-sizing: border-box;
    }

    /* ─── ERP Cards ────────────────────────────────────────────────────── */
    .inv-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        margin-bottom: 22px;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .inv-card-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 60%, var(--erp-blue-light) 100%);
        color: #ffffff !important;
        padding: 14px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 11px;
        border-top-right-radius: 11px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .inv-card-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.2px;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .inv-card-header .hdr-icon {
        font-size: 16px;
        color: #ffffff !important;
        opacity: 0.95;
    }

    .inv-card-body {
        padding: 20px 22px;
        background: #ffffff;
    }

    /* ─── Grid Form Layout (4 columns row 1, 3 columns row 2) ─────────── */
    .search-grid-row-1 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .search-grid-row-2 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .search-grid-row-1, .search-grid-row-2 {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }

    @media (max-width: 576px) {
        .search-grid-row-1, .search-grid-row-2 {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }

    .erp-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--erp-text-dark);
        margin-bottom: 6px;
        display: block;
    }

    .erp-input {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 7px;
        font-size: 13px;
        color: #0f172a;
        padding: 7px 12px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        height: 40px;
        box-sizing: border-box;
    }

    .erp-input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        outline: none;
    }

    /* ─── Buttons ──────────────────────────────────────────────────────── */
    .btn-inv-search {
        background: linear-gradient(135deg, var(--erp-blue) 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        padding: 8px 32px;
        border-radius: 7px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .btn-inv-search:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    }

    .btn-inv-discard {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        padding: 8px 30px;
        border-radius: 7px;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }
    .btn-inv-discard:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
    }

    .btn-download-excel {
        background: #10b981 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 12.5px;
        padding: 6px 16px;
        border-radius: 6px;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-download-excel:hover {
        background: #059669 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    /* ─── Scrollable Table Wrapper (Pixel-Perfect Matching Image 1) ────── */
    .table-scroll-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch;
        display: block;
        padding-bottom: 8px;
        margin: 0;
    }

    .table-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 6px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    .table-payment-custom {
        min-width: 1100px !important;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    .table-payment-custom thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 12.5px;
        font-weight: 800;
        text-transform: capitalize;
        letter-spacing: 0.3px;
        padding: 13px 14px;
        border-bottom: 1.5px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }

    .table-payment-custom tbody td {
        padding: 13px 14px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-payment-custom tbody tr:hover td {
        background-color: #f8fbff;
    }

    .table-payment-custom tfoot td {
        background: #ffffff;
        padding: 14px 14px;
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        border-top: 2px solid #cbd5e1;
        white-space: nowrap;
    }

    /* ─── Paid Amount Bold Green ───────────────────────────────────────── */
    .amount-paid-green {
        color: var(--erp-paid-green) !important;
        font-weight: 800 !important;
        font-size: 13.5px;
    }

    /* ─── View Receipt Action Button (Matching Image 1) ───────────────── */
    .btn-action-view-receipt {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        background-color: #eff6ff;
        color: #2563eb;
        border: 1.5px solid #bfdbfe;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(37, 99, 235, 0.08);
    }
    .btn-action-view-receipt:hover {
        background-color: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .btn-action-view-receipt i {
        font-size: 12px;
    }

    .invoice-link-style {
        font-weight: 600;
        color: #0f172a;
        text-decoration: none;
    }
    .invoice-link-style:hover {
        color: var(--erp-blue);
        text-decoration: underline;
    }

    /* ─── Custom Pagination ─────────────────────────────────────────────── */
    .custom-pagination-wrap {
        display: flex;
        justify-content: center;
        padding: 14px 0 6px;
    }
    .custom-pagination-wrap .pagination {
        margin: 0;
        gap: 4px;
    }
    .custom-pagination-wrap .page-link {
        color: #334155;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .custom-pagination-wrap .page-item.active .page-link {
        background-color: var(--erp-blue);
        border-color: var(--erp-blue);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
</style>

<div class="inv-container">
    <!-- 1. SEARCH SECTION CARD (Top Section - Matching Image 1 & 2) -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5>
                <i class="fas fa-search hdr-icon"></i>
                <span>Search Partial Payments</span>
            </h5>
        </div>
        <div class="inv-card-body">
            <form method="GET" action="{{ route('school.inventory.payment-history') }}" id="search-filter-form">
                <!-- Row 1: Order No., Invoice No., Student Name, Mobile No. (4 Columns) -->
                <div class="search-grid-row-1">
                    <div>
                        <label class="erp-label">Order No.</label>
                        <input type="text" name="order_no" class="erp-input" placeholder="Enter Order No." value="{{ request('order_no', $orderNo ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">Invoice No.</label>
                        <input type="text" name="invoice_no" class="erp-input" placeholder="Enter Invoice No." value="{{ request('invoice_no', $invoiceNo ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">Student Name</label>
                        <input type="text" name="student_name" class="erp-input" placeholder="Enter Student Name" value="{{ request('student_name', $studentName ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">Mobile No.</label>
                        <input type="text" name="mobile_no" class="erp-input" placeholder="Enter Mobile No." value="{{ request('mobile_no', $mobileNo ?? '') }}">
                    </div>
                </div>

                <!-- Row 2: From Date, To Date, Payment Mode (3 Columns) -->
                <div class="search-grid-row-2">
                    <div>
                        <label class="erp-label">From Date</label>
                        <input type="date" name="from_date" class="erp-input" value="{{ request('from_date', $fromDate ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">To Date</label>
                        <input type="date" name="to_date" class="erp-input" value="{{ request('to_date', $toDate ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">Payment Mode</label>
                        <select name="payment_mode" class="erp-input">
                            <option value="">-- All --</option>
                            <option value="cash" {{ (strtolower(request('payment_mode', $paymentMode ?? '')) == 'cash') ? 'selected' : '' }}>Cash</option>
                            <option value="online" {{ (in_array(strtolower(request('payment_mode', $paymentMode ?? '')), ['online', 'upi'])) ? 'selected' : '' }}>Online</option>
                            <option value="card" {{ (strtolower(request('payment_mode', $paymentMode ?? '')) == 'card') ? 'selected' : '' }}>Card / POS</option>
                            <option value="cheque" {{ (strtolower(request('payment_mode', $paymentMode ?? '')) == 'cheque') ? 'selected' : '' }}>Cheque</option>
                            <option value="dd" {{ (in_array(strtolower(request('payment_mode', $paymentMode ?? '')), ['dd', 'demand_draft'])) ? 'selected' : '' }}>Demand Draft</option>
                            <option value="bank_transfer" {{ (strtolower(request('payment_mode', $paymentMode ?? '')) == 'bank_transfer') ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons Centered -->
                <div class="text-center d-flex justify-content-center gap-3">
                    <button type="submit" class="btn-inv-search">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('school.inventory.payment-history') }}" class="btn-inv-discard">
                        <i class="fas fa-rotate-left"></i> Discard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. PARTIAL PAYMENT TRANSACTIONS TABLE CARD (Matching Image 1 & 2) -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5>
                <i class="fas fa-table-list hdr-icon"></i>
                <span>Partial Payment Transactions</span>
            </h5>
            <!-- Excel Export Button -->
            <div>
                <button type="button" class="btn-download-excel" onclick="exportPaymentTableCSV()">
                    <i class="fas fa-file-excel"></i> Download Excel
                </button>
            </div>
        </div>
        <div class="inv-card-body p-0">
            <!-- Scrollable Table Container -->
            <div class="table-scroll-wrapper">
                <table class="table table-payment-custom align-middle" id="payment-history-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">S/N</th>
                            <th>Receipt ID</th>
                            <th>Order No.</th>
                            <th>Invoice No.</th>
                            <th>Student Name</th>
                            <th>Mobile</th>
                            <th class="text-end">Paid Amount</th>
                            <th>Payment Mode</th>
                            <th>Ref / Txn ID</th>
                            <th>Payment Date</th>
                            <th class="text-center" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $calculatedPageTotal = 0;
                        @endphp
                        @forelse($payments as $index => $pay)
                            @php
                                $rowNumber = (is_object($payments) && method_exists($payments, 'firstItem') && $payments->firstItem()) ? ($payments->firstItem() + $index) : ($index + 1);
                                
                                $receiptId = $pay->receipt_id ?? ($pay->receipt_number ?: $pay->id);
                                $orderNum = $pay->order_no ?? ($pay->reference_no ?: ($pay->id >= 1000 ? $pay->id : (1000 + $pay->id)));
                                $invoiceNumber = $pay->invoice_number ?? ('INV-' . $pay->id);
                                $studentName = $pay->customer_name ?? ($pay->student?->full_name ?? '—');
                                $mobile = $pay->customer_mobile ?? ($pay->student?->phone ?? '—');
                                $paidAmt = floatval($pay->paid_amount ?? 0);
                                $calculatedPageTotal += $paidAmt;
                                
                                $paymentModeLabel = is_object($pay) && isset($pay->payment_mode_label) 
                                    ? $pay->payment_mode_label 
                                    : ucfirst($pay->payment_mode ?? 'Cash');
                                    
                                $refTxn = !empty($pay->reference_no) ? $pay->reference_no : '—';
                                
                                $paymentDate = !empty($pay->sale_date) 
                                    ? \Carbon\Carbon::parse($pay->sale_date)->format('d-m-Y h:i A') 
                                    : (!empty($pay->created_at) ? \Carbon\Carbon::parse($pay->created_at)->format('d-m-Y h:i A') : '—');
                                    
                                $receiptUrl = route('school.inventory.billing.receipt', $pay->id);
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $rowNumber }}</td>
                                <td class="fw-bold text-dark">{{ $receiptId }}</td>
                                <td class="text-dark">{{ $orderNum }}</td>
                                <td>
                                    <a href="{{ $receiptUrl }}" target="_blank" class="invoice-link-style" title="View Printable Receipt">
                                        {{ $invoiceNumber }}
                                    </a>
                                </td>
                                <td class="fw-bold text-dark">{{ $studentName }}</td>
                                <td class="text-muted">{{ $mobile }}</td>
                                <td class="text-end amount-paid-green">{{ number_format($paidAmt, 2) }}</td>
                                <td>{{ $paymentModeLabel }}</td>
                                <td class="text-muted">{{ $refTxn }}</td>
                                <td class="text-muted">{{ $paymentDate }}</td>
                                <td class="text-center">
                                    <a href="{{ $receiptUrl }}" target="_blank" class="btn-action-view-receipt" title="View and Print Official Receipt">
                                        <i class="fas fa-file-lines"></i> View Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                    No partial payment transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-bold" style="font-size: 14px;">Page Total:</td>
                            <td class="text-end amount-paid-green" style="font-size: 14.5px;">
                                {{ number_format($pageTotal ?? $calculatedPageTotal, 2) }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Custom Centered Pagination -->
        @if(is_object($payments) && method_exists($payments, 'hasPages') && $payments->hasPages())
            <div class="custom-pagination-wrap border-top bg-white py-3">
                {{ $payments->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

<script>
    /**
     * Client-side CSV / Excel generator to ensure 100% reliable instant download
     */
    function exportPaymentTableCSV() {
        const table = document.getElementById('payment-history-table');
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll('td, th');
            // Skip action column (last column)
            for (let j = 0; j < cols.length - 1; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s+)/gm, ' ').trim();
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(','));
        }

        const csvString = csv.join('\n');
        const filename = 'Partial_Payment_Transactions_' + new Date().toISOString().slice(0, 10) + '.csv';
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename);
        } else {
            const link = document.createElement('a');
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
</script>
@endsection
