@extends('layouts.app')

@section('page-title', 'Fee Invoice 1')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice" style="color:var(--gold);margin-right:8px;"></i>Modern Invoice Template 1</h1>
        <p>Preview and print custom styled receipt-style invoice templates for parents</p>
    </div>
</div>

<div class="grid-3">
    <!-- Invoice Selection Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Modern Invoice Configuration</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Select Student Account</label>
                <select id="invoiceStudent1" class="form-control" onchange="loadInvoiceDetails1(this.value)">
                    <option value="">Select Student</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                    @endforeach
                </select>
            </div>
            <p style="color:var(--t2); font-size:12px; margin-bottom:16px;">
                Prints a modern slip-style layout with a bottom sign-off section and payment slip stub.
            </p>
            <button class="btn btn-gold" style="width:100%; justify-content:center;" onclick="printInvoice1()">
                <i class="fas fa-print"></i> Print Receipt Slip
            </button>
        </div>
    </div>

    <!-- Modern Invoice View -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Receipt-Slip View</h3>
        </div>
        <div class="card-body" id="invoicePrintArea1" style="background:#f1f5f9; padding:20px; font-family:'Courier New', monospace;">
            <div style="background:#fff; border:2px dashed #94a3b8; padding:25px; color:#0f172a;">
                <!-- Logo Stub -->
                <div style="text-align:center; margin-bottom:15px; border-bottom:1px dashed #cbd5e1; padding-bottom:10px;">
                    <h2 style="font-size:16px; font-weight:800; margin:0; text-transform:uppercase;">Yash International School</h2>
                    <span style="font-size:11px; color:#64748b;">OFFICIAL FEE RECEIPT DEPOSIT SLIP</span>
                </div>

                <!-- Info Block -->
                <div style="font-size:12px; line-height:1.6; margin-bottom:15px;">
                    <div><strong>RECEIPT ID:</strong> LNK-{{ date('Ymd') }}-{{ rand(10, 99) }}</div>
                    <div><strong>DATE:</strong> {{ date('Y-m-d H:i:s') }}</div>
                    <div><strong>STUDENT NAME:</strong> <span id="slipName">Aarav Sharma</span></div>
                    <div><strong>ADMISSION ID:</strong> <span id="slipID">YIS/2026/00001</span></div>
                    <div><strong>CLASS GRADE:</strong> <span id="slipClass">Class 10 - Section A</span></div>
                </div>

                <!-- Receipt Table -->
                <table style="width:100%; font-size:12px; border-collapse:collapse; margin-bottom:15px;">
                    <thead>
                        <tr style="border-bottom:1px dashed #000; font-weight:700;">
                            <th style="text-align:left; padding:4px 0;">FEE CATEGORY PARTICULAR</th>
                            <th style="text-align:right; padding:4px 0;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="slipTableBody">
                        <tr>
                            <td style="padding:4px 0;">Tuition Course Fee</td>
                            <td style="padding:4px 0; text-align:right;">₹2,500.00</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0;">Examination Fee</td>
                            <td style="padding:4px 0; text-align:right;">₹500.00</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Total Row -->
                <div style="border-top:1px dashed #000; padding-top:10px; font-size:13px; text-align:right; font-weight:700; margin-bottom:25px;">
                    TOTAL CHARGES DUE: <span id="slipTotal">₹3,000.00</span>
                </div>

                <!-- Bottom sign-off -->
                <div style="display:flex; justify-content:space-between; font-size:11px; margin-top:20px;">
                    <div style="text-align:center; width:120px; border-top:1px solid #000; padding-top:4px;">Parent Signature</div>
                    <div style="text-align:center; width:120px; border-top:1px solid #000; padding-top:4px;">Authorized Cashier</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const mockSlipInvoices = {
    @foreach($students as $st)
    "{{ $st->id }}": {
        name: "{{ $st->full_name }}",
        class: "{{ optional($st->class)->name ?? 'N/A' }} - {{ optional($st->section)->name ?? 'N/A' }}",
        id: "{{ $st->admission_id }}",
        fees: [
            @foreach($fees->where('student_id', $st->id) as $f)
            { desc: "{{ $f->category->name }}", amt: {{ $f->amount }} },
            @endforeach
        ]
    },
    @endforeach
};

function loadInvoiceDetails1(studentId) {
    if(!studentId || !mockSlipInvoices[studentId]) return;
    const inv = mockSlipInvoices[studentId];
    document.getElementById('slipName').textContent = inv.name;
    document.getElementById('slipClass').textContent = inv.class;
    document.getElementById('slipID').textContent = inv.id;
    
    let html = '';
    let total = 0;
    inv.fees.forEach(f => {
        html += `<tr>
            <td style="padding:4px 0;">${f.desc}</td>
            <td style="padding:4px 0; text-align:right; font-weight:700;">₹${f.amt.toFixed(2)}</td>
        </tr>`;
        total += f.amt;
    });
    
    if (inv.fees.length === 0) {
        html = `<tr><td colspan="2" style="text-align:center; padding:8px; color:#64748b;">No outstanding fees.</td></tr>`;
    }
    
    document.getElementById('slipTableBody').innerHTML = html;
    document.getElementById('slipTotal').textContent = '₹' + total.toFixed(2);
}

function printInvoice1() {
    const printContents = document.getElementById('invoicePrintArea1').innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
@endsection
