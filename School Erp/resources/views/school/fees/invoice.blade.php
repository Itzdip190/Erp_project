@extends('layouts.app')

@section('page-title', 'Fee Invoice')

@section('styles')
<style>
    .school-logo-badge {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #0f3a4c;
        color: #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    
    @media print {
        body * {
            visibility: hidden !important;
        }
        #invoicePrintArea, #invoicePrintArea * {
            visibility: visible !important;
        }
        #invoicePrintArea {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            padding: 20px !important;
            border: none !important;
            box-shadow: none !important;
            background: #ffffff !important;
        }
        .page-hdr, .sidebar, .topbar, .card-hdr, .grid-3 > div:first-child {
            display: none !important;
        }
    }
</style>
@endsection

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
                        <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->full_name }} ({{ $st->admission_id }})
                        </option>
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
            <!-- School Header Table -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px; border-bottom:2px solid #0f3a4c; padding-bottom:15px;">
                <tr>
                    <td style="vertical-align:top; padding-bottom:15px;">
                        <div style="display:flex; align-items:center; gap:14px;">
                            @if(isset($school) && !empty($school->logo) && Storage::disk('public')->exists($school->logo))
                                <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="Logo" style="max-height:50px; max-width:120px; object-fit:contain;">
                            @else
                                <div class="school-logo-badge">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            @endif
                            <div>
                                <h2 style="font-family:'Plus Jakarta Sans', sans-serif; font-weight:800; color:#0f3a4c; font-size:20px; margin:0; line-height:1.2;">
                                    {{ $school->name ?? 'SchoolCloud ERP Institution' }}
                                </h2>
                                <p style="font-size:12px; color:#6b7280; margin:3px 0 0 0;">
                                    {{ $school->address ?? '128, Academic Avenue, Tech City, India' }}
                                </p>
                                @if($config && $config->inst_affiliation_no)
                                <p style="font-size:11px; color:#6b7280; margin:2px 0 0 0;">Affiliation No: AFF-98762</p>
                                @endif
                                @if($config && $config->inst_school_url)
                                <p style="font-size:11px; color:#6b7280; margin:2px 0 0 0;">Website: www.pragyaschool.edu</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="vertical-align:top; text-align:right; padding-bottom:15px;">
                        @if($config && $config->inst_board_logo)
                        <div style="margin-bottom:6px;"><span class="badge badge-warning" style="background:rgba(245,158,11,0.15); color:#d97706; padding:4px 8px; border-radius:4px; font-size:11px; font-weight:700;"><i class="fas fa-certificate"></i> CBSE Board</span></div>
                        @endif
                        <h3 style="font-size:16px; color:#d97706; text-transform:uppercase; font-weight:800; margin:0 0 4px 0;">Fee Invoice</h3>
                        <p style="font-size:12px; color:#9ca3af; margin:0;">Invoice Ref: <strong id="invRef" style="color:#0f3a4c;">INV-{{ date('Y') }}-0092</strong></p>
                    </td>
                </tr>
            </table>

            <!-- Student Metadata Table -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:13px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
                <tr>
                    <td style="padding:15px; vertical-align:top; width:60%;">
                        <span style="color:#9ca3af; text-transform:uppercase; font-size:10px; font-weight:700; display:block; margin-bottom:4px;">Bill To:</span>
                        @if(!$config || $config->details_student_name)
                        <div style="font-weight:800; color:#0f3a4c; font-size:15px; margin-bottom:4px;" id="invStudentName">Aarav Sharma</div>
                        @endif
                        @if(!$config || $config->details_class)
                        <div style="color:#475569; margin-bottom:2px;" id="invStudentClass">Grade Class: Class 10 - Section A</div>
                        @endif
                        @if(!$config || $config->details_admission_no)
                        <div style="color:#475569; margin-bottom:2px;" id="invStudentID">Admission ID: YIS/2026/00001</div>
                        @endif
                        @if($config && $config->details_father_name)
                        <div style="color:#475569; margin-bottom:2px;" id="invStudentFather">Father's Name: <span id="invFatherName">—</span></div>
                        @endif
                        @if($config && $config->details_mother_name)
                        <div style="color:#475569; margin-bottom:2px;" id="invStudentMother">Mother's Name: <span id="invMotherName">—</span></div>
                        @endif
                        @if($config && $config->details_address)
                        <div style="color:#475569; margin-bottom:2px;" id="invStudentAddress">Address: <span id="invAddress">—</span></div>
                        @endif
                        @if($config && $config->details_father_phone)
                        <div style="color:#475569; margin-bottom:2px;" id="invStudentPhone">Phone: <span id="invFatherPhone">—</span></div>
                        @endif
                    </td>
                    <td style="padding:15px; vertical-align:top; text-align:right; width:40%; color:#475569;">
                        <div style="margin-bottom:6px;"><strong>Invoice Date:</strong> {{ date('Y-m-d') }}</div>
                        <div><strong>Due Date:</strong> {{ date('Y-m-d', strtotime('+7 days')) }}</div>
                    </td>
                </tr>
            </table>

            <!-- Invoice Particulars Table -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:13px; border:1px solid #cbd5e1;">
                <thead>
                    <tr style="border-bottom:2px solid #cbd5e1; background:#0f3a4c; color:#ffffff;">
                        <th style="padding:10px 14px; text-align:left; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">Particulars Description</th>
                        <th style="padding:10px 14px; text-align:right; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">Billing Cycle</th>
                        <th style="padding:10px 14px; text-align:right; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">Original Amount</th>
                        <th style="padding:10px 14px; text-align:right; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">Discount</th>
                        <th style="padding:10px 14px; text-align:right; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">Paid</th>
                        <th style="padding:10px 14px; text-align:right; font-weight:700; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">Net Due</th>
                    </tr>
                </thead>
                <tbody id="invoiceTableBody">
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:12px 14px; color:#1e293b;">Tuition Course Fee Allocation</td>
                        <td style="padding:12px 14px; text-align:right; color:#475569;">Monthly</td>
                        <td style="padding:12px 14px; text-align:right; font-weight:700; color:#0f172a;">₹2,500.00</td>
                        <td style="padding:12px 14px; text-align:right; font-weight:700; color:#dc2626;">₹0.00</td>
                        <td style="padding:12px 14px; text-align:right; font-weight:700; color:#16a34a;">₹0.00</td>
                        <td style="padding:12px 14px; text-align:right; font-weight:700; color:#0f3a4c;">₹2,500.00</td>
                    </tr>
                </tbody>
            </table>

            <!-- Invoice Totals Table -->
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:40%;"></td>
                    <td style="width:60%;">
                        <table style="width:100%; font-size:13px; border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px 0; color:#64748b;">Subtotal (Original):</td>
                                <td style="padding:6px 0; text-align:right; font-weight:700; color:#0f172a;" id="invSubtotal">₹2,500.00</td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0; color:#64748b;">Total Discount:</td>
                                <td style="padding:6px 0; text-align:right; font-weight:700; color:#dc2626;" id="invDiscount">₹0.00</td>
                            </tr>
                            <tr style="border-bottom:1px solid #cbd5e1;">
                                <td style="padding:6px 0; color:#64748b;">Total Paid:</td>
                                <td style="padding:6px 0; text-align:right; font-weight:700; color:#16a34a;" id="invPaid">₹0.00</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; font-size:16px; font-weight:800; color:#0f3a4c;">Net Outstanding:</td>
                                <td style="padding:10px 0; text-align:right; font-size:16px; font-weight:800; color:#0f3a4c;" id="invTotal">₹2,500.00</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
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
        father: "{{ $st->father_name ?? '—' }}",
        mother: "{{ $st->mother_name ?? '—' }}",
        address: "{{ $st->address ?? '—' }}",
        phone: "{{ $st->father_phone ?? '—' }}",
        fees: [
            @foreach($fees->where('student_id', $st->id) as $f)
            { 
                desc: "{{ $f->component ? $f->component->component_name : ($f->category ? $f->category->name : 'Fee Due') }}", 
                cycle: "{{ $f->feeSchedule ? $f->feeSchedule->name : ($f->category ? $f->category->name : 'Billing Cycle') }}", 
                amt: {{ $f->amount }},
                discount: {{ $f->instant_discount_amount ?? 0 }},
                paid: {{ $f->paid_amount ?? 0 }},
                invoice_no: "{{ $f->invoice_no ?? '' }}"
            },
            @endforeach
        ]
    },
    @endforeach
};

function loadInvoiceDetails(studentId) {
    if(!studentId || !mockInvoices[studentId]) return;
    const inv = mockInvoices[studentId];
    if (document.getElementById('invStudentName')) document.getElementById('invStudentName').textContent = inv.name;
    if (document.getElementById('invStudentClass')) document.getElementById('invStudentClass').textContent = 'Grade Class: ' + inv.class;
    if (document.getElementById('invStudentID')) document.getElementById('invStudentID').textContent = 'Admission ID: ' + inv.id;
    if (document.getElementById('invFatherName')) document.getElementById('invFatherName').textContent = inv.father;
    if (document.getElementById('invMotherName')) document.getElementById('invMotherName').textContent = inv.mother;
    if (document.getElementById('invAddress')) document.getElementById('invAddress').textContent = inv.address;
    if (document.getElementById('invFatherPhone')) document.getElementById('invFatherPhone').textContent = inv.phone;
    
    let html = '';
    let totalAmt = 0;
    let totalDiscount = 0;
    let totalPaid = 0;
    let totalNetDue = 0;
    let invNo = 'INV-{{ date('Y') }}-' + Math.floor(1000 + Math.random() * 9000);
    
    inv.fees.forEach(f => {
        const netDue = Math.max(0, f.amt - f.discount - f.paid);
        html += `<tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:12px 14px; color:#1e293b;">${f.desc}</td>
            <td style="padding:12px 14px; text-align:right; color:#475569;">${f.cycle}</td>
            <td style="padding:12px 14px; text-align:right; font-weight:700; color:#0f172a;">₹${f.amt.toFixed(2)}</td>
            <td style="padding:12px 14px; text-align:right; font-weight:700; color:#dc2626;">₹${f.discount.toFixed(2)}</td>
            <td style="padding:12px 14px; text-align:right; font-weight:700; color:#16a34a;">₹${f.paid.toFixed(2)}</td>
            <td style="padding:12px 14px; text-align:right; font-weight:700; color:#0f3a4c;">₹${netDue.toFixed(2)}</td>
        </tr>`;
        totalAmt += f.amt;
        totalDiscount += f.discount;
        totalPaid += f.paid;
        totalNetDue += netDue;
        if (f.invoice_no) {
            invNo = f.invoice_no;
        }
    });
    
    if (inv.fees.length === 0) {
        html = `<tr><td colspan="6" style="text-align:center; padding:15px; color:#9ca3af;">No outstanding fees.</td></tr>`;
    }
    
    document.getElementById('invoiceTableBody').innerHTML = html;
    document.getElementById('invSubtotal').textContent = '₹' + totalAmt.toFixed(2);
    document.getElementById('invDiscount').textContent = '₹' + totalDiscount.toFixed(2);
    document.getElementById('invPaid').textContent = '₹' + totalPaid.toFixed(2);
    document.getElementById('invTotal').textContent = '₹' + totalNetDue.toFixed(2);
    
    if (document.getElementById('invRef')) {
        document.getElementById('invRef').textContent = invNo;
    }
}

function printInvoice() {
    window.print();
}

$(document).ready(function() {
    let studentId = $('#invoiceStudent').val();
    if (studentId) {
        loadInvoiceDetails(studentId);
    }
});
</script>
@endsection
