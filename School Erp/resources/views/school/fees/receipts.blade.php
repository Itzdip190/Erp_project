@extends('layouts.app')

@section('page-title', 'Fee Receipts')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice-dollar" style="color:var(--gold);margin-right:8px;"></i>Fee Receipts Directory</h1>
        <p>Access, print, and audit all transaction receipts issued for student fee collections</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Fee Receipts Ledger</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name</th>
                        <th>Class & Section</th>
                        <th>Amount Paid</th>
                        <th>Payment Mode</th>
                        <th>Transaction ID</th>
                        <th>Date Paid</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td><strong style="color:var(--gold);">{{ $receipt->receipt_number }}</strong></td>
                        <td>
                            <strong style="color:var(--navy);">{{ $receipt->student->full_name }}</strong>
                            <small style="display:block; color:var(--t3);">{{ $receipt->student->admission_id }}</small>
                        </td>
                        <td>{{ optional($receipt->student->class)->name ?? 'N/A' }} - {{ optional($receipt->student->section)->name ?? 'N/A' }}</td>
                        <td><strong>₹{{ number_format($receipt->amount_paid, 2) }}</strong></td>
                        <td><span class="badge badge-success">{{ ucfirst($receipt->payment_mode) }}</span></td>
                        <td><span style="font-family:monospace;">{{ $receipt->transaction_id ?? '—' }}</span></td>
                        <td>{{ $receipt->payment_date }}</td>
                        <td>
                            <button class="btn btn-outline" style="padding:4px 8px; font-size:11px;" onclick="showReceiptDetails('{{ $receipt->receipt_number }}', '{{ $receipt->student->full_name }}', '{{ number_format($receipt->amount_paid, 2) }}', '{{ $receipt->payment_mode }}', '{{ $receipt->payment_date }}')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px; color:var(--t3);">No receipts recorded in system.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Simple Detail Modal -->
<div id="receiptModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div class="card" style="width:400px; background:#fff; margin-bottom:0;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 id="modalTitle">Receipt Details</h3>
            <button class="btn btn-outline" style="padding:2px 8px;" onclick="document.getElementById('receiptModal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <div class="card-body">
            <div style="text-align:center; margin-bottom:20px;">
                <div style="font-size:32px; color:var(--green);"><i class="fas fa-check-circle"></i></div>
                <h2 id="modalAmount" style="font-size:24px; font-weight:800; margin:10px 0;">₹0.00</h2>
                <p style="color:var(--t2);">Payment Successfully Received</p>
            </div>
            <table style="width:100%; font-size:13px; border-collapse:collapse; margin-bottom:20px;">
                <tr style="border-bottom:1px solid var(--border);"><td style="padding:8px 0; color:var(--t2);">Student Name</td><td id="modalStudent" style="padding:8px 0; text-align:right; font-weight:700;">—</td></tr>
                <tr style="border-bottom:1px solid var(--border);"><td style="padding:8px 0; color:var(--t2);">Receipt Number</td><td id="modalNumber" style="padding:8px 0; text-align:right; font-weight:700;">—</td></tr>
                <tr style="border-bottom:1px solid var(--border);"><td style="padding:8px 0; color:var(--t2);">Payment Mode</td><td id="modalMode" style="padding:8px 0; text-align:right; text-transform:uppercase;">—</td></tr>
                <tr style="border-bottom:1px solid var(--border);"><td style="padding:8px 0; color:var(--t2);">Payment Date</td><td id="modalDate" style="padding:8px 0; text-align:right;">—</td></tr>
            </table>
            <button class="btn btn-gold" style="width:100%; justify-content:center;" onclick="window.print()">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>
</div>

<script>
function showReceiptDetails(number, student, amount, mode, date) {
    document.getElementById('modalTitle').textContent = 'Receipt: ' + number;
    document.getElementById('modalNumber').textContent = number;
    document.getElementById('modalStudent').textContent = student;
    document.getElementById('modalAmount').textContent = '₹' + amount;
    document.getElementById('modalMode').textContent = mode;
    document.getElementById('modalDate').textContent = date;
    document.getElementById('receiptModal').style.display = 'flex';
}
</script>
@endsection
