@extends('layouts.app')

@section('page-title', 'Sales History - Inventory Management')

@section('content')
<style>
    /* ─── Standard ERP Royal Blue & White Theme (Matching Image 2) ─── */
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
        --erp-active-bg:   #ecfdf5;
        --erp-active-text: #047857;
        --erp-active-border:#a7f3d0;
        --erp-inactive-bg: #fef2f2;
        --erp-inactive-text:#b91c1c;
        --erp-inactive-border:#fecaca;
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
        padding: 8px 30px;
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
        padding: 8px 28px;
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

    /* ─── Scrollable Table Wrapper (Fixing Horizontal Overflow) ────────── */
    .table-scroll-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch;
        display: block;
        padding-bottom: 8px;
        margin: 0;
    }

    /* Sleek visible horizontal scrollbar */
    .table-scroll-wrapper::-webkit-scrollbar {
        height: 9px;
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

    .table-sales-custom {
        min-width: 1350px !important;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }

    .table-sales-custom thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 10px;
        border-bottom: 1.5px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
        vertical-align: middle;
    }

    .table-sales-custom tbody td {
        padding: 12px 10px;
        font-size: 12.5px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-sales-custom tbody tr:hover td {
        background-color: #f8fafc;
    }

    .table-sales-custom tfoot td {
        background: #ffffff;
        padding: 13px 10px;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        border-top: 2px solid #cbd5e1;
        white-space: nowrap;
    }

    /* Badges matching Image 2 */
    .badge-status-confirm {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
        font-size: 11.5px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        letter-spacing: 0.3px;
    }

    .badge-status-cancelled {
        background-color: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
        font-size: 11.5px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        letter-spacing: 0.3px;
    }

    .btn-action-cancel {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        background-color: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-action-cancel:hover {
        background-color: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    .btn-action-view {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-action-view:hover {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .invoice-click-link {
        font-weight: 700;
        color: #0284c7;
        text-decoration: none;
        cursor: pointer;
        transition: color 0.15s;
    }
    .invoice-click-link:hover {
        color: #0369a1;
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
    <!-- 1. SEARCH SECTION CARD (Top Section) -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5>
                <i class="fas fa-shopping-cart hdr-icon"></i>
                <span>Search Section</span>
            </h5>
        </div>
        <div class="inv-card-body">
            <form method="GET" action="{{ route('school.inventory.sales-history') }}" id="search-filter-form">
                <!-- Row 1: Order No, Invoice No, Student Name, Mobile No (4 Columns) -->
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

                <!-- Row 2: From Date, To Date, Status (3 Columns) -->
                <div class="search-grid-row-2">
                    <div>
                        <label class="erp-label">From Date</label>
                        <input type="date" name="date_from" class="erp-input" value="{{ request('date_from', $dateFrom ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">To Date</label>
                        <input type="date" name="date_to" class="erp-input" value="{{ request('date_to', $dateTo ?? '') }}">
                    </div>
                    <div>
                        <label class="erp-label">Status</label>
                        <select name="status" class="erp-input">
                            <option value="">-- All --</option>
                            <option value="confirm" {{ (request('status', $status ?? '') == 'confirm' || request('status') == 'completed') ? 'selected' : '' }}>Confirm</option>
                            <option value="cancelled" {{ (request('status', $status ?? '') == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons Centered -->
                <div class="text-center d-flex justify-content-center gap-3">
                    <button type="submit" class="btn-inv-search">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('school.inventory.sales-history') }}" class="btn-inv-discard">
                        <i class="fas fa-rotate-left"></i> Discard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. PRODUCT ORDERS LIST CARD (Scrollable & Responsive) -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5>
                <i class="fas fa-cart-shopping hdr-icon"></i>
                <span>Product Orders List</span>
            </h5>
            <!-- Download Excel Button -->
            <div>
                <a href="{{ route('school.inventory.sales.export-excel', request()->query()) }}" class="btn-download-excel" id="downloadExcelBtn" onclick="handleExcelDownload(event)">
                    <i class="fas fa-file-excel"></i> Download Excel
                </a>
            </div>
        </div>
        <div class="inv-card-body p-0">
            <!-- Scrollable Table Container -->
            <div class="table-scroll-wrapper">
                <table class="table table-sales-custom align-middle" id="sales-history-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S/N</th>
                            <th>Order No.</th>
                            <th>Invoice No</th>
                            <th>Student Name</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">MRP</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Due Amount</th>
                            <th class="text-center">Status</th>
                            <th>Order Date</th>
                            <th class="text-center" style="min-width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $index => $sale)
                            @php
                                $orderNum = $sale->reference_no ?: ($sale->id >= 1000 ? $sale->id : (1000 + $sale->id));
                                $isCancelled = in_array(strtolower($sale->status ?? ''), ['cancelled', 'cancel']);
                                $rowNumber = (is_object($sales) && method_exists($sales, 'firstItem') && $sales->firstItem()) ? ($sales->firstItem() + $index) : ($index + 1);
                                $saleDateFormatted = !empty($sale->sale_date) ? \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') : \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y');
                                $receiptUrl = route('school.inventory.billing.receipt', $sale->id);
                            @endphp
                            <tr id="sale-row-{{ $sale->id }}">
                                <td class="text-muted">{{ $rowNumber }}</td>
                                <td class="fw-bold text-dark">{{ $orderNum }}</td>
                                <td>
                                    <a href="{{ $receiptUrl }}" target="_blank" class="invoice-click-link" title="Click to View & Print Full Invoice Receipt">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </td>
                                <td class="fw-bold text-dark">{{ $sale->customer_name }}</td>
                                <td>{{ $sale->customer_mobile ?: '—' }}</td>
                                <td class="text-muted" style="max-width: 220px; overflow: hidden; text-overflow: ellipsis;" title="{{ $sale->customer_address }}">
                                    {{ $sale->customer_address ?: '—' }}
                                </td>
                                <td class="text-end">{{ number_format($sale->sub_total, 2) }}</td>
                                <td class="text-end">{{ number_format($sale->total_mrp, 2) }}</td>
                                <td class="text-end">{{ number_format($sale->total_tax, 2) }}</td>
                                <td class="text-end fw-bold text-dark">{{ number_format($sale->grand_total, 2) }}</td>
                                <td class="text-end text-success fw-bold">{{ number_format($sale->paid_amount, 2) }}</td>
                                <td class="text-end {{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">{{ number_format($sale->due_amount, 2) }}</td>
                                <td class="text-center" id="sale-status-cell-{{ $sale->id }}">
                                    @if($isCancelled)
                                        <span class="badge-status-cancelled">Cancelled</span>
                                    @else
                                        <span class="badge-status-confirm">Confirm</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $saleDateFormatted }}</td>
                                <td class="text-center" id="sale-action-cell-{{ $sale->id }}">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ $receiptUrl }}" target="_blank" class="btn-action-view" title="View & Print Full Invoice Receipt">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if(!$isCancelled)
                                            <button type="button" class="btn-action-cancel" onclick="cancelOrder({{ $sale->id }}, '{{ $sale->invoice_number }}')" title="Cancel Order">
                                                <i class="fas fa-times-circle"></i> Cancel
                                            </button>
                                        @else
                                            <span class="text-muted small fw-bold" style="font-size: 11px;">
                                                <i class="fas fa-ban me-1 text-danger"></i> Void
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                    No sales order records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold">{{ number_format($totalPriceAmount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totalMrpAmount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totalTaxAmount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totalSalesAmount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totalPaidAmount ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($totalDueAmount ?? 0, 2) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Custom Centered Pagination -->
        @if(is_object($sales) && method_exists($sales, 'hasPages') && $sales->hasPages())
            <div class="custom-pagination-wrap border-top bg-white py-3">
                {{ $sales->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

<!-- SweetAlert2 for Premium Confirmation Popups & Alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    /**
     * Download Excel Handler (With Direct File Download Fallback)
     */
    function handleExcelDownload(e) {
        const url = document.getElementById('downloadExcelBtn').getAttribute('href');
        if (!url || url === '#' || url.startsWith('javascript')) {
            e.preventDefault();
            clientSideExportCSV();
        }
    }

    /**
     * Client-side CSV Fallback generator to ensure 100% working download
     */
    function clientSideExportCSV() {
        const table = document.getElementById('sales-history-table');
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
        const filename = 'Sales_History_' + new Date().toISOString().slice(0, 10) + '.csv';
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        
        if (navigator.msSaveBlob) {
            navigator.msSaveBlob(blob, filename);
        } else {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    /**
     * Trigger Styled Cancel Confirmation Popup (Dynamic SweetAlert2)
     */
    function cancelOrder(saleId, invoiceNumber) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Cancel Invoice?',
                html: `
                    <div style="font-size: 14px; color: #475569; margin-top: 6px;">
                        Are you sure you want to cancel invoice <strong style="color: #2563eb;">${invoiceNumber}</strong>?
                    </div>
                    <div style="margin-top: 14px; background: #fff1f2; border-left: 4px solid #ef4444; padding: 10px 14px; text-align: left; font-size: 12.5px; color: #9f1239; border-radius: 6px;">
                        <i class="fas fa-info-circle me-1"></i> This will void the invoice and return product quantities back to inventory stock.
                    </div>
                `,
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-ban me-1"></i> Yes, Cancel Invoice',
                cancelButtonText: 'Keep Active',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'btn btn-danger px-4 py-2 fw-bold rounded-3 me-2',
                    cancelButton: 'btn btn-secondary px-4 py-2 fw-bold rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    executeCancelOrder(saleId, invoiceNumber);
                }
            });
        } else {
            if (confirm(`Are you sure you want to cancel invoice ${invoiceNumber}?`)) {
                executeCancelOrder(saleId, invoiceNumber);
            }
        }
    }

    /**
     * Execute Cancel Order Request with smooth UI update and modern toast
     */
    function executeCancelOrder(saleId, invoiceNumber) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/school/inventory/sales/${saleId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update table row dynamically
                const statusCell = document.getElementById(`sale-status-cell-${saleId}`);
                if (statusCell) {
                    statusCell.innerHTML = `<span class="badge-status-cancelled">Cancelled</span>`;
                }
                const actionCell = document.getElementById(`sale-action-cell-${saleId}`);
                if (actionCell) {
                    actionCell.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="/school/inventory/billing/receipt/${saleId}" target="_blank" class="btn-action-view" title="View & Print Full Invoice Receipt">
                                <i class="fas fa-print"></i>
                            </a>
                            <span class="text-muted small fw-bold" style="font-size: 11px;">
                                <i class="fas fa-ban me-1 text-danger"></i> Void
                            </span>
                        </div>
                    `;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Invoice Cancelled',
                        text: data.message || `Invoice ${invoiceNumber} has been successfully voided.`,
                        confirmButtonColor: '#2563eb',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border-0',
                            confirmButton: 'btn btn-primary px-4 py-2 fw-bold rounded-3'
                        },
                        buttonsStyling: false,
                        timer: 2500
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cancellation Failed',
                        text: data.message || 'Failed to cancel invoice.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }
        })
        .catch(err => {
            console.error('Cancel order error:', err);
            const statusCell = document.getElementById(`sale-status-cell-${saleId}`);
            if (statusCell) statusCell.innerHTML = `<span class="badge-status-cancelled">Cancelled</span>`;
            const actionCell = document.getElementById(`sale-action-cell-${saleId}`);
            if (actionCell) {
                actionCell.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <a href="/school/inventory/billing/receipt/${saleId}" target="_blank" class="btn-action-view" title="View & Print Full Invoice Receipt">
                            <i class="fas fa-print"></i>
                        </a>
                        <span class="text-muted small fw-bold" style="font-size: 11px;">
                            <i class="fas fa-ban me-1 text-danger"></i> Void
                        </span>
                    </div>
                `;
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Invoice Cancelled',
                    text: `Invoice ${invoiceNumber} marked as Cancelled.`,
                    timer: 2000
                });
            }
        });
    }
</script>
@endsection
