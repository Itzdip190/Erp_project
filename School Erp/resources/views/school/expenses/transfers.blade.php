@extends('layouts.app')

@php
    $sch = app()->bound('currentSchool') ? app('currentSchool') : (auth()->check() ? auth()->user()->school : null);
    $sess = $sch ? \App\Models\AcademicSession::where('school_id', $sch->id)->where('is_current', true)->first() : null;
@endphp

@section('title', 'Account Transfers')

@section('styles')
<style>
/* ─── VARIABLES ──────────────────────────────── */
:root {
    --exp-blue:      #3b82f6;
    --exp-blue-dark: #1d4ed8;
    --exp-blue-light:#eff6ff;
    --exp-white:     #ffffff;
    --exp-gray:      #f8fafc;
    --exp-border:    #cbd5e1;
    --exp-text:      #1e293b;
    --exp-text2:     #64748b;
    --exp-green:     #10b981;
    --exp-green-hover:#059669;
    --exp-red:       #ef4444;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.05);
    --shadow-md: 0 4px 16px rgba(0,0,0,.08);
}
body.dark-mode {
    --exp-white:     #111827;
    --exp-gray:      #1f2937;
    --exp-border:    #374151;
    --exp-text:      #f8fafc;
    --exp-text2:     #94a3b8;
    --exp-blue-light:rgba(59, 130, 246, 0.15);
}

.exp-container {
    padding: 24px;
    width: 100%;
}
.exp-card {
    background: var(--exp-white);
    border: 1px solid var(--exp-border);
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
}
.exp-card-body {
    padding: 20px;
}

/* ─── FILTER BAR ─────────────────────────────── */
.filter-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.filter-control {
    height: 34px;
    padding: 6px 12px;
    border: 1px solid var(--exp-border);
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    background: var(--exp-white);
    color: var(--exp-text);
    outline: none;
}
.exp-btn-go {
    background-color: #2563eb;
    color: #fff;
    border: none;
    height: 34px;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    text-transform: uppercase;
}
.exp-btn-go:hover { background-color: #1d4ed8; }

.exp-btn-clear {
    background-color: #94a3b8;
    color: #fff;
    border: none;
    height: 34px;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    text-transform: uppercase;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.exp-btn-clear:hover { background-color: #64748b; }

/* ─── BAR ROW ────────────────────────────────── */
.bar-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.pill-btn-blue {
    background-color: #2563eb;
    color: #fff;
    border: none;
    padding: 8px 20px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 20px;
    cursor: pointer;
    text-transform: uppercase;
    box-shadow: 0 2px 5px rgba(37, 99, 235, 0.2);
}
.pill-btn-blue:hover {
    background-color: #1d4ed8;
}
.pill-btn-print {
    background-color: var(--exp-green);
    color: #fff;
    border: none;
    padding: 8px 24px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 20px;
    cursor: pointer;
    text-transform: uppercase;
}
.pill-btn-print:hover {
    background-color: var(--exp-green-hover);
}

/* ─── TABLE ──────────────────────────────────── */
.exp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.exp-table th {
    background: var(--exp-gray);
    color: var(--exp-text);
    font-weight: 700;
    padding: 12px 14px;
    border-bottom: 2px solid var(--exp-border);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.3px;
    text-align: left;
}
.exp-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--exp-border);
    color: var(--exp-text);
    vertical-align: middle;
}
.exp-table tr:hover {
    background: var(--exp-blue-light);
}

/* ─── MODALS ─────────────────────────────────── */
.exp-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.25s ease;
}
.exp-modal-overlay.open { opacity: 1; pointer-events: auto; }
.exp-modal {
    background: var(--exp-white);
    border: 1px solid var(--exp-border);
    border-radius: 12px;
    width: 100%; max-width: 480px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.25s ease;
}
.exp-modal-overlay.open .exp-modal { transform: translateY(0); }
.exp-modal-hdr {
    background: #0ea5e9;
    padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.exp-modal-hdr h3 { margin: 0; font-size: 15px; font-weight: 700; }
.modal-close { background: none; border: none; color: #fff; font-size: 18px; cursor: pointer; }
.exp-modal-body { padding: 20px; }
.form-group { margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; font-weight: 700; color: var(--exp-text); margin-bottom: 4px; }
.form-group label span { color: var(--exp-red); }
.form-control {
    width: 100%; height: 36px; padding: 6px 10px;
    border: 1px solid var(--exp-border); border-radius: 6px;
    font-size: 12.5px; font-weight: 500; font-family: inherit;
    background: var(--exp-white); color: var(--exp-text);
    outline: none;
}
.modal-footer { display: flex; align-items: center; justify-content: center; margin-top: 18px; }
.exp-btn-submit {
    background: none; border: 1px solid #3b82f6; color: #3b82f6;
    padding: 8px 30px; font-size: 12px; font-weight: 700; border-radius: 20px;
    cursor: pointer; transition: all 0.2s; text-transform: uppercase;
}
.exp-btn-submit:hover { background: #3b82f6; color: #fff; }

/* Toast */
#exp-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 2500;
    display: flex; flex-direction: column; gap: 10px;
}
.toast-msg {
    background: var(--exp-white); border: 1px solid var(--exp-border);
    padding: 12px 20px; border-radius: 10px; box-shadow: var(--shadow-md);
    display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600;
    animation: slideIn 0.3s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Printable Invoice Modal Styles */
#invoiceReceiptModal .exp-modal {
    max-width: 650px;
    background: #fff;
    color: #000;
}
body.dark-mode #invoiceReceiptModal .exp-modal {
    background: #fff !important;
    color: #000 !important;
}
body.dark-mode #invoiceReceiptModal .modal-close {
    color: #000 !important;
}
.receipt-card {
    background: #fff;
    padding: 24px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-family: 'Outfit', 'Inter', sans-serif;
    color: #000 !important;
}
.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #000;
    padding-bottom: 12px;
    margin-bottom: 16px;
}
.receipt-logo {
    width: 64px;
    height: 64px;
    object-fit: contain;
}
.receipt-logo-placeholder {
    width: 64px;
    height: 64px;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #94a3b8;
}
.receipt-school-info {
    text-align: right;
}
.receipt-school-name {
    font-size: 18px;
    font-weight: 800;
    text-transform: uppercase;
    color: #1e3a8a;
    margin: 0;
}
.receipt-school-address {
    font-size: 11px;
    color: #475569;
    margin: 2px 0 0 0;
}
.receipt-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.receipt-title {
    font-size: 14px;
    font-weight: 800;
    border: 1.5px solid #000;
    padding: 3px 10px;
    text-transform: uppercase;
    color: #000 !important;
}
.receipt-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px 24px;
    font-size: 12px;
    margin-bottom: 16px;
    background: #f8fafc;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
.receipt-meta-item {
    display: flex;
    justify-content: space-between;
}
.receipt-meta-lbl {
    font-weight: 700;
    color: #475569;
}
.receipt-meta-val {
    font-weight: 600;
    color: #0f172a;
}
.receipt-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 12px;
    color: #000 !important;
}
.receipt-table th, .receipt-table td {
    border: 1px solid #cbd5e1;
    padding: 8px 12px;
    color: #000 !important;
}
.receipt-table th {
    background: #f1f5f9;
    font-weight: 700;
    text-transform: uppercase;
    text-align: left;
}
.receipt-words {
    font-size: 11px;
    font-style: italic;
    margin-bottom: 16px;
    font-weight: 600;
    color: #334155;
    text-transform: uppercase;
}
.receipt-signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 36px;
    font-size: 12px;
}
.receipt-sig-line {
    border-top: 1px solid #000;
    width: 120px;
    text-align: center;
    padding-top: 4px;
    font-weight: 700;
}

/* Printing styles */
@media print {
    .sidebar, .topbar, .exp-container, .exp-modal-hdr, .receipt-actions-row, .modal-close {
        display: none !important;
    }
    .main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .pg {
        padding: 0 !important;
        margin: 0 !important;
    }
    #invoiceReceiptModal {
        position: relative !important;
        background: #fff !important;
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        z-index: auto !important;
        inset: auto !important;
    }
    #invoiceReceiptModal .exp-modal {
        box-shadow: none !important;
        border: none !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
        background: #fff !important;
    }
    .receipt-card {
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection

@section('content')
<div class="exp-container">
    <div class="exp-card">
        <div class="exp-card-body">
            {{-- SEARCH FILTERS --}}
            <form method="GET" action="{{ route('school.expenses.transfers') }}">
                <div class="filter-row">
                    <input type="date" class="filter-control" name="start_date" value="{{ $startDate }}" required>
                    <input type="date" class="filter-control" name="end_date" value="{{ $endDate }}" required>
                    <button type="submit" class="exp-btn-go">Go</button>
                    <a href="{{ route('school.expenses.transfers') }}" class="exp-btn-clear">Clear</a>
                </div>
            </form>

            {{-- INTERACTION BAR --}}
            <div class="bar-row">
                <button class="pill-btn-blue" id="addTransferBtn">Account Transfers</button>
                <button class="pill-btn-print" onclick="openPrintAllConfig('transfer')">Print All Transfers <i class="fas fa-print"></i></button>
            </div>

            {{-- LIST TABLE --}}
            <div style="overflow-x: auto; border: 1px solid var(--exp-border); border-radius: 8px;">
                <table class="exp-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>From Account</th>
                            <th>To Account</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Remarks</th>
                            <th style="width: 100px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $idx => $t)
                        <tr id="row-{{ $t->id }}">
                            <td style="font-weight: 600; color: var(--exp-text2);">{{ $idx + 1 }}</td>
                            <td style="font-weight: 700;">{{ $t->from_account }}</td>
                            <td style="font-weight: 700;">{{ $t->to_account }}</td>
                            <td style="font-weight: 700; color: var(--exp-blue-dark);">₹{{ number_format($t->amount, 2) }}</td>
                            <td>{{ $t->transfer_date ? $t->transfer_date->format('d M Y') : '' }}</td>
                            <td>{{ $t->remarks ?? '—' }}</td>
                            <td style="text-align: center;">
                                <button class="pill-btn-print" onclick="window.open('{{ route("school.expenses.print-all") }}?ids={{ $t->id }}&per_page=1&type=transfer&print=1', '_blank', 'width=950,height=750')" title="Print Transfer Receipt" style="padding: 4px 12px; font-size: 11px; border-radius: 12px;">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--exp-text2); padding: 40px;">
                                No account transfers logged in this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- PRINT ALL CONFIG MODAL --}}
<div class="exp-modal-overlay" id="printAllModal">
    <div class="exp-modal" style="max-width: 450px;">
        <div class="exp-modal-hdr" style="background: var(--exp-blue-dark);">
            <h3><i class="fas fa-print"></i> Print All Transfers</h3>
            <button class="modal-close" onclick="closePrintAllModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body" style="padding: 20px;">
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 700; margin-bottom: 8px; display: block; font-size:13px; color:var(--exp-text);">How many transfers per A4 page?</label>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                    <div style="border: 1.5px solid var(--exp-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-1" onclick="selectPerPage(1)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">1</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">Full Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--exp-blue); background: var(--exp-blue-light); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-2" onclick="selectPerPage(2)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">2</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">Half Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--exp-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-3" onclick="selectPerPage(3)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">3</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">1/3 Page</span>
                    </div>
                    <div style="border: 1.5px solid var(--exp-border); border-radius: 8px; padding: 10px; text-align: center; cursor: pointer;" class="per-page-opt" id="opt-4" onclick="selectPerPage(4)">
                        <strong style="display: block; font-size: 16px; color:var(--exp-text);">4</strong>
                        <span style="font-size: 10px; color: var(--exp-text2);">1/4 Page</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="exp-btn exp-btn-outline" style="padding: 8px 16px; font-weight:700; border-radius:8px; border: 1.5px solid var(--exp-border); cursor:pointer;" onclick="closePrintAllModal()">Cancel</button>
                <button type="button" class="exp-btn" style="padding: 8px 16px; font-weight:700; border-radius:8px; width:auto; border: 1px solid var(--exp-blue); background:var(--exp-blue); color:#fff;" onclick="submitPrintAll()">
                    Print <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- INVOICE RECEIPT MODAL --}}
<div class="exp-modal-overlay" id="invoiceReceiptModal">
    <div class="exp-modal" style="max-width: 650px;">
        <div class="exp-modal-hdr" style="background: #1e3a8a;">
            <h3>Transfer Voucher Receipt</h3>
            <button class="modal-close" onclick="closeInvoiceReceiptModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body" style="background: #fff; padding: 20px;">
            <div id="receiptPrintArea" class="receipt-card">
                <div class="receipt-header">
                    <div class="receipt-brand">
                        @if($sch && !empty($sch->logo) && Storage::disk('public')->exists($sch->logo))
                            <img class="receipt-logo" src="{{ Storage::disk('public')->url($sch->logo) }}" alt="{{ $sch->name }}">
                        @else
                            <div class="receipt-logo-placeholder">
                                <i class="fas fa-school"></i>
                            </div>
                        @endif
                    </div>
                    <div class="receipt-school-info">
                        <h2 class="receipt-school-name">{{ $sch->name ?? 'Lord Krishna Educational Academy' }}</h2>
                        <p class="receipt-school-address">{{ $sch->address ?? 'Agra Road Mainpuri' }}</p>
                        <p class="receipt-school-address">Phone: {{ $sch->phone ?? 'N/A' }} | Session: {{ $sess->session_title ?? '2026-27' }}</p>
                    </div>
                </div>
                
                <div class="receipt-title-row">
                    <span class="receipt-title">Transfer Voucher</span>
                    <span style="font-size:11.5px; font-weight:700; color:#475569;">Date: <span id="recDate"></span></span>
                </div>

                <div class="receipt-meta-grid">
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">Transfer Ref ID:</span>
                        <span class="receipt-meta-val" id="recRefId"></span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">Processed By:</span>
                        <span class="receipt-meta-val" id="recCreator" style="text-transform: capitalize;"></span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">From Account (Debit):</span>
                        <span class="receipt-meta-val" id="recFromAccount"></span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-lbl">To Account (Credit):</span>
                        <span class="receipt-meta-val" id="recToAccount"></span>
                    </div>
                </div>

                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S.No.</th>
                            <th>Description / Particulars</th>
                            <th style="width: 140px; text-align: right;">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td id="recParticulars"></td>
                            <td style="text-align: right; font-weight:700;" id="recTableAmount"></td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td colspan="2" style="text-align: right; font-weight: 700;">Total Transferred:</td>
                            <td style="text-align: right; font-weight: 700;" id="recTotalAmount"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="receipt-words">
                    Amount in words: (<span id="recWords"></span>)
                </div>

                <div class="receipt-signatures">
                    <div class="receipt-sig-line" style="border-top:none;">Approved By</div>
                    <div class="receipt-sig-line">Receiver Sig.</div>
                </div>
            </div>

            <div class="modal-footer receipt-actions-row">
                <button type="button" class="exp-btn-clear" onclick="closeInvoiceReceiptModal()" style="height: auto; padding: 8px 18px; font-size: 13px; text-transform: none;">Close & Sync</button>
                <button type="button" class="pill-btn-blue" onclick="printReceipt()" style="background:#10b981; border-color:#10b981;">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ADD TRANSFER MODAL --}}
<div class="exp-modal-overlay" id="transferModal">
    <div class="exp-modal">
        <div class="exp-modal-hdr">
            <h3>Record Account Transfer</h3>
            <button class="modal-close" onclick="closeTransferModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body">
            <form id="transferForm">
                @csrf
                <div class="form-group">
                    <label>From Account <span>*</span></label>
                    <select class="form-control" name="from_account" required>
                        <option value="">Select Account</option>
                        <option value="CASH">CASH</option>
                        <option value="SBI Bank">SBI Bank</option>
                        <option value="HDFC Bank">HDFC Bank</option>
                        <option value="Petty Cash">Petty Cash</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>To Account <span>*</span></label>
                    <select class="form-control" name="to_account" required>
                        <option value="">Select Account</option>
                        <option value="CASH">CASH</option>
                        <option value="SBI Bank">SBI Bank</option>
                        <option value="HDFC Bank">HDFC Bank</option>
                        <option value="Petty Cash">Petty Cash</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount <span>*</span></label>
                    <input type="number" class="form-control" name="amount" placeholder="0.00" min="0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Transfer Date <span>*</span></label>
                    <input type="date" class="form-control" name="transfer_date" id="tDate" required>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <input type="text" class="form-control" name="remarks" placeholder="Optional notes">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="exp-btn-submit" id="transferSubmitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="exp-toast"></div>
@endsection

@section('scripts')
<script>
const tModal = document.getElementById('transferModal');
const tForm  = document.getElementById('transferForm');

document.getElementById('addTransferBtn').addEventListener('click', () => {
    document.getElementById('tDate').value = new Date().toISOString().split('T')[0];
    tModal.classList.add('open');
});
function closeTransferModal() { tModal.classList.remove('open'); tForm.reset(); }

tForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Simple validation
    const fromAcc = tForm.elements['from_account'].value;
    const toAcc   = tForm.elements['to_account'].value;
    if (fromAcc === toAcc) {
        showToast('Source and destination accounts must be different.', 'error');
        return;
    }

    const btn = document.getElementById('transferSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Submitting...';

    const data = Object.fromEntries(new FormData(tForm));

    try {
        const res = await fetch('{{ route("school.expenses.transfers.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            closeTransferModal();
            showTransferReceipt(json.transfer);
        } else {
            showToast(json.message || 'Error recording transfer.', 'error');
        }
    } catch(err) {
        showToast('Network error.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Submit';
    }
});

function showToast(msg, type = 'success') {
    const toast = document.getElementById('exp-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + type;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// Eager load serialised transfers for front-end lookup
const allTransfers = @json($transfers);

const receiptModal = document.getElementById('invoiceReceiptModal');

// Number to Words converter (Indian formatting)
function numberToWords(num) {
    const a = ['', 'one ', 'two ', 'three ', 'four ', 'five ', 'six ', 'seven ', 'eight ', 'nine ', 'ten ', 'eleven ', 'twelve ', 'thirteen ', 'fourteen ', 'fifteen ', 'sixteen ', 'seventeen ', 'eighteen ', 'nineteen '];
    const b = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    
    num = Math.floor(num);
    if (num === 0) return 'zero';
    
    function g(n) {
        if (n < 20) return a[n];
        let d = n % 10;
        return b[Math.floor(n / 10)] + (d ? '-' + a[d] : ' ');
    }
    
    let str = '';
    let crore = Math.floor(num / 10000000);
    num %= 10000000;
    let lakh = Math.floor(num / 100000);
    num %= 100000;
    let thousand = Math.floor(num / 1000);
    num %= 1000;
    let hundred = Math.floor(num / 100);
    num %= 100;
    
    if (crore) str += g(crore) + 'crore ';
    if (lakh) str += g(lakh) + 'lakh ';
    if (thousand) str += g(thousand) + 'thousand ';
    if (hundred) str += g(hundred) + 'hundred ';
    if (num) {
        if (str !== '') str += 'and ';
        str += g(num);
    }
    return str.trim() + ' only';
}

function openTransferReceiptModal(id) {
    const t = allTransfers.find(item => item.id == id);
    if (t) {
        showTransferReceipt(t);
    }
}

function showTransferReceipt(t) {
    const dateObj = new Date(t.transfer_date);
    const formattedDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    
    document.getElementById('recDate').textContent = formattedDate;
    document.getElementById('recRefId').textContent = 'TR-' + t.id;
    document.getElementById('recCreator').textContent = t.creator ? t.creator.name : 'N/A';
    document.getElementById('recFromAccount').textContent = t.from_account;
    document.getElementById('recToAccount').textContent = t.to_account;
    
    document.getElementById('recParticulars').textContent = `Transfer of funds from ${t.from_account} to ${t.to_account}` + (t.remarks ? ` (${t.remarks})` : '');
    
    const amountVal = parseFloat(t.amount);
    document.getElementById('recTableAmount').textContent = amountVal.toFixed(2);
    document.getElementById('recTotalAmount').textContent = '₹' + amountVal.toFixed(2);
    
    document.getElementById('recWords').textContent = numberToWords(amountVal);
    
    receiptModal.classList.add('open');
}

function closeInvoiceReceiptModal() {
    receiptModal.classList.remove('open');
    location.reload();
}

function printReceipt() {
    window.print();
}

// ─── PRINT ALL CONFIG CONTROLLERS ────────────────────────────────────
let printAllType = 'transfer';
let selectedPerPage = 2;

function openPrintAllConfig(type) {
    printAllType = type;
    document.getElementById('printAllModal').classList.add('open');
}

function closePrintAllModal() {
    document.getElementById('printAllModal').classList.remove('open');
}

function selectPerPage(num) {
    selectedPerPage = num;
    document.querySelectorAll('.per-page-opt').forEach(el => {
        el.style.borderColor = 'var(--exp-border)';
        el.style.background = 'transparent';
    });
    const selectedOpt = document.getElementById('opt-' + num);
    if (selectedOpt) {
        selectedOpt.style.borderColor = 'var(--exp-blue)';
        selectedOpt.style.background = 'var(--exp-blue-light)';
    }
}

function submitPrintAll() {
    const rows = document.querySelectorAll('tbody tr[id^="row-"]');
    const ids = [];
    rows.forEach(r => {
        const id = r.getAttribute('id').replace('row-', '');
        ids.push(id);
    });
    
    if (ids.length === 0) {
        alert('No records available to print.');
        return;
    }
    
    closePrintAllModal();
    const url = '{{ route("school.expenses.print-all") }}?ids=' + ids.join(',') + '&per_page=' + selectedPerPage + '&type=' + printAllType + '&print=1';
    window.open(url, '_blank', 'width=950,height=750');
}
</script>
@endsection
