@extends('layouts.app')

@section('page-title', 'Fee Invoice')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice" style="color:var(--gold);margin-right:8px;"></i>Standard Fee Invoice Generator</h1>
        <p>Preview and print standardized billing invoices for student tuition and mapped fee requirements</p>
    </div>
</div>

<div class="grid-3">
    <!-- Invoice Selection Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Invoice Selection</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Select Student</label>
                <select id="invoiceStudent" class="form-control" onchange="loadInvoiceDetails(this.value)">
                    <option value="">Select Student</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                    @endforeach
                </select>
            </div>
            <p style="color:var(--t2); font-size:12px; margin-bottom:16px;">
                Selecting a student will pull outstanding balances, academic class info, and print options.
            </p>
            <button class="btn btn-gold" style="width:100%; justify-content:center;" onclick="printInvoice()">
                <i class="fas fa-print"></i> Print Invoice
            </button>
        </div>
    </div>

    <!-- Invoice Preview Card -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Standard Invoice Preview</h3>
        </div>
        <div class="card-body" id="invoicePrintArea" style="background:#fff; border:1px solid var(--border); padding:30px; font-family:'Inter', sans-serif;">
            <!-- School Header -->
            <div style="display:flex; justify-content:space-between; border-bottom:2px solid var(--navy); padding-bottom:15px; margin-bottom:20px;">
                <div>
                    <h2 style="font-family:'Plus Jakarta Sans', sans-serif; font-weight:800; color:var(--navy); font-size:18px;">SchoolCloud ERP Institution</h2>
                    <p style="font-size:12px; color:var(--t2);">128, Academic Avenue, Tech City, India</p>
                </div>
                <div style="text-align:right;">
                    <h3 style="font-size:14px; color:var(--gold); text-transform:uppercase; font-weight:700;">Fee Invoice</h3>
                    <p style="font-size:11px; color:var(--t3);">Invoice Ref: INV-{{ date('Y') }}-0092</p>
                </div>
            </div>

            <!-- Student Metadata -->
            <div style="display:flex; justify-content:space-between; margin-bottom:20px; font-size:12.5px;">
                <div>
                    <span style="color:var(--t3); text-transform:uppercase; font-size:10px; font-weight:700;">Bill To:</span>
                    <div style="font-weight:700; color:var(--navy); margin-top:2px;" id="invStudentName">Aarav Sharma</div>
                    <div id="invStudentClass">Grade Class: Class 10 - Section A</div>
                    <div id="invStudentID">Admission ID: YIS/2026/00001</div>
                </div>
                <div style="text-align:right;">
                    <div><strong>Invoice Date:</strong> {{ date('Y-m-d') }}</div>
                    <div><strong>Due Date:</strong> {{ date('Y-m-d', strtotime('+7 days')) }}</div>
                </div>
            </div>

            <!-- Invoice Particulars -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:13px;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border); background:#f9fafb;">
                        <th style="padding:8px; text-align:left; color:var(--t2);">Particulars Description</th>
                        <th style="padding:8px; text-align:right; color:var(--t2);">Billing Cycle</th>
                        <th style="padding:8px; text-align:right; color:var(--t2);">Amount</th>
                    </tr>
                </thead>
                <tbody id="invoiceTableBody">
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:10px 8px;">Tuition Course Fee Allocation</td>
                        <td style="padding:10px 8px; text-align:right;">Monthly</td>
                        <td style="padding:10px 8px; text-align:right; font-weight:700;">₹2,500.00</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:10px 8px;">Academic Examination Assessment Fee</td>
                        <td style="padding:10px 8px; text-align:right;">Quarterly</td>
                        <td style="padding:10px 8px; text-align:right; font-weight:700;">₹500.00</td>
                    </tr>
                </tbody>
            </table>

            <!-- Invoice Totals -->
            <div style="display:flex; justify-content:flex-end;">
                <div style="width:200px; font-size:13px;">
                    <div style="display:flex; justify-content:space-between; padding:4px 0;">
                        <span>Subtotal:</span>
                        <strong id="invSubtotal">₹3,000.00</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px solid var(--border);">
                        <span>Taxes / Fine:</span>
                        <strong>₹0.00</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:8px 0; font-size:15px; color:var(--navy);">
                        <span>Total Due:</span>
                        <strong id="invTotal" style="color:var(--navy); font-weight:800;">₹3,000.00</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mock student data loaded to preview area
const mockInvoices = {
    @foreach($students as $st)
    "{{ $st->id }}": {
        name: "{{ $st->full_name }}",
        class: "{{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}",
        id: "{{ $st->admission_id }}",
        fees: [
            @foreach($fees->where('student_id', $st->id) as $f)
            { desc: "{{ $f->category->name }}", cycle: "Term billing", amt: {{ $f->amount }} },
            @endforeach
        ]
    },
    @endforeach
};

function loadInvoiceDetails(studentId) {
    if(!studentId || !mockInvoices[studentId]) return;
    const inv = mockInvoices[studentId];
    document.getElementById('invStudentName').textContent = inv.name;
    document.getElementById('invStudentClass').textContent = 'Grade Class: ' + inv.class;
    document.getElementById('invStudentID').textContent = 'Admission ID: ' + inv.id;
    
    let html = '';
    let total = 0;
    inv.fees.forEach(f => {
        html += `<tr style="border-bottom:1px solid var(--border);">
            <td style="padding:10px 8px;">${f.desc}</td>
            <td style="padding:10px 8px; text-align:right;">${f.cycle}</td>
            <td style="padding:10px 8px; text-align:right; font-weight:700;">₹${f.amt.toFixed(2)}</td>
        </tr>`;
        total += f.amt;
    });
    
    if (inv.fees.length === 0) {
        html = `<tr><td colspan="3" style="text-align:center; padding:15px; color:var(--t3);">No outstanding fees.</td></tr>`;
    }
    
    document.getElementById('invoiceTableBody').innerHTML = html;
    document.getElementById('invSubtotal').textContent = '₹' + total.toFixed(2);
    document.getElementById('invTotal').textContent = '₹' + total.toFixed(2);
}

function printInvoice() {
    const printContents = document.getElementById('invoicePrintArea').innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
@endsection
