@extends('layouts.app')
@section('page-title', 'Vehicle Expenses')
@section('content')
@include('school.transport.partials.tp-styles')

<style>
.exp-ledger-row {
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    transition: background 0.15s ease;
}
.exp-ledger-row:hover {
    background: rgba(37, 99, 235, 0.015);
}
.exp-ledger-row:last-child {
    border-bottom: none;
}

.tp-cat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 10px;
}
.tp-cat-btn {
    padding: 12px 6px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    background: var(--white);
    color: var(--t2);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.tp-cat-btn:hover {
    border-color: var(--cc);
    color: var(--cc);
    transform: translateY(-2px);
}
.tp-cat-btn.active {
    border-color: var(--cc) !important;
    background: var(--cbg) !important;
    color: var(--cc) !important;
    box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.08);
}
.tp-filedrop {
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: var(--page);
}
.tp-filedrop:hover, .tp-filedrop.drag-over {
    border-color: #2563eb;
    background: rgba(37, 99, 235, 0.03);
}
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-receipt" style="color:var(--gold);margin-right:8px;"></i>Vehicle Expenses</h1>
        <p>Track fuel, servicing, repairs, and other fleet costs with receipt attachments</p>
    </div>
    <div class="page-hdr-right">
        <button class="btn btn-gold" onclick="openAddModal()"><i class="fas fa-plus"></i><span>Log Expense</span></button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<!-- Summary statistics strip -->
<div class="tp-stats">
    <div class="tp-stat" style="--sc:#1e40af;--sb:#eff6ff;">
        <div class="tp-stat-icon"><i class="fas fa-wallet"></i></div>
        <div>
            <div class="tp-stat-label">Total Spent</div>
            <div class="tp-stat-val">₹{{ number_format($expenses->sum('amount'),2) }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#2563eb;--sb:#eff6ff;">
        <div class="tp-stat-icon"><i class="fas fa-gas-pump"></i></div>
        <div>
            <div class="tp-stat-label">Fuel</div>
            <div class="tp-stat-val">₹{{ number_format($expenses->where('expense_type','Fuel')->sum('amount'),2) }}</div>
        </div>
    </div>
    <div class="tp-stat" style="--sc:#0ea5e9;--sb:#e0f2fe;">
        <div class="tp-stat-icon"><i class="fas fa-tools"></i></div>
        <div>
            <div class="tp-stat-label">Service</div>
            <div class="tp-stat-val">₹{{ number_format($expenses->where('expense_type','Servicing')->sum('amount'),2) }}</div>
        </div>
    </div>
</div>

<!-- Ledger container -->
<div class="card" style="border-radius:16px; border:1px solid var(--border); overflow:hidden; margin-bottom: 24px;">
    <div class="tp-card-hdr">
        <h3>Expense Ledger</h3>
        <span class="tp-badge tp-badge-gold">{{ $expenses->count() }} Record{{ $expenses->count()!=1?'s':'' }}</span>
    </div>
    <div style="padding:14px 20px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; border-bottom:1px solid var(--border); background:rgba(0,0,0,0.005);">
        <div style="position:relative; flex:1; min-width:250px;">
            <i class="fas fa-search" style="position:absolute; left:14px; top:13px; color:var(--t3); font-size:14px;"></i>
            <input type="text" id="ledgerSearch" class="form-control" placeholder="Search by type, vehicle, or description..." style="padding-left:36px; height:40px; font-size:13px;" oninput="filterLedger()">
        </div>
        <button type="button" class="btn btn-outline" style="height:40px; display:inline-flex; align-items:center; gap:8px;" onclick="exportLedgerToCSV()">
            <i class="fas fa-file-csv" style="font-size: 15px; color: #16a34a;"></i><span>Export CSV</span>
        </button>
    </div>
    
    <div>
        @forelse($expenses as $ex)
        <div class="exp-ledger-row">
            <div style="width:42px; height:42px; border-radius:10px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas {{ match($ex->expense_type){
                    'Fuel' => 'fa-gas-pump', 'Servicing' => 'fa-tools',
                    'Tyres' => 'fa-circle-notch', 'Insurance' => 'fa-shield-alt',
                    'Challan' => 'fa-file-alt', default => 'fa-receipt'
                } }}" style="color:#2563eb; font-size:16px;"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; font-size:14px; color:var(--t1); margin-bottom:2px;">{{ $ex->expense_type }}</div>
                <div style="font-size:12px; color:var(--t2); font-weight:500;">
                    <span style="font-weight:700; color:var(--t1);">{{ $ex->vehicle?->vehicle_no }}</span> &middot; {{ $ex->date->format('d M, Y') }}{{ $ex->description ? ' &middot; '.$ex->description : '' }}
                </div>
            </div>
            @if($ex->attachment)
            <a href="{{ asset('storage/'.$ex->attachment) }}" target="_blank" class="tp-attach-link" title="View receipt" style="margin-right: 8px;">
                <i class="fas fa-paperclip"></i><span>Bill</span>
            </a>
            @endif
            <div style="font-weight:800; font-size:16px; color:#2563eb; white-space:nowrap; margin:0 12px;">₹{{ number_format($ex->amount,2) }}</div>
            <div style="display:flex; gap:6px;">
                <button class="tp-btn-edit" onclick="expEdit({{ json_encode($ex) }})" title="Edit"><i class="fa fa-edit"></i></button>
                <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete this record?')">
                    @csrf 
                    <input type="hidden" name="id" value="{{ $ex->id }}">
                    <input type="hidden" name="type" value="expense">
                    <button type="submit" class="tp-btn-del" title="Delete"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="tp-empty"><i class="fas fa-receipt"></i><p>No expenses logged yet.</p></div>
        @endforelse
    </div>
</div>

<!-- ── Expense Entry Form Modal ── -->
<div class="tp-modal-overlay" id="expenseModal">
    <div class="tp-modal">
        <div class="tp-modal-hdr">
            <h3 id="expFormTitle"><i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Log Expense</h3>
            <button class="tp-modal-close" onclick="closeModal('expenseModal')">&times;</button>
        </div>
        <div class="tp-modal-body">
            <form method="POST" action="{{ route('school.transport.expenses') }}" id="expForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="expId">
                
                <div class="form-group">
                    <label class="form-label">Vehicle <span style="color:var(--red);">*</span></label>
                    <select name="vehicle_id" id="expVehicle" class="form-control" required>
                        <option value="">— Select Vehicle —</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->vehicle_no }} ({{ $v->driver_name ?: 'No Driver' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Expense Category <span style="color:var(--red);">*</span></label>
                    <div class="tp-cat-grid" id="catGrid">
                        @foreach([['Fuel','#1e40af','#eff6ff','fa-gas-pump'],['Servicing','#2563eb','#eff6ff','fa-tools'],['Tyres','#3b82f6','#eff6ff','fa-circle-notch'],['Insurance','#0284c7','#eff6ff','fa-shield-alt'],['Challan','#0ea5e9','#eff6ff','fa-file-alt'],['Other','#64748b','#f8fafc','fa-ellipsis-h']] as $cat)
                        <button type="button" class="tp-cat-btn" data-cat="{{ $cat[0] }}" data-cc="{{ $cat[1] }}" data-cbg="{{ $cat[2] }}"
                            style="--cc:{{ $cat[1] }};--cbg:{{ $cat[2] }};"
                            onclick="setCategory('{{ $cat[0] }}','{{ $cat[1] }}','{{ $cat[2] }}',this)">
                            <i class="fas {{ $cat[3] }}" style="display:block; font-size:18px; margin-bottom:4px; color:{{ $cat[1] }};"></i>{{ $cat[0] }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="expense_type" id="expType" required>
                    
                    <!-- Dynamic Other Category Input -->
                    <div id="otherCategoryWrap" style="display:none; margin-top:12px;">
                        <label class="form-label" style="color:var(--t2);">Custom Category Name <span style="color:var(--red);">*</span></label>
                        <input type="text" id="otherCategoryName" class="form-control" placeholder="e.g. Battery, Toll Tax, Oil Filter" oninput="updateOtherType(this.value)">
                    </div>
                </div>
                
                <div class="tp-g2">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Amount ₹ <span style="color:var(--red);">*</span></label>
                        <input type="number" step="0.01" name="amount" id="expAmount" class="form-control" placeholder="0.00" min="0" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Date <span style="color:var(--red);">*</span></label>
                        <input type="date" name="date" id="expDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 18px;">
                    <label class="form-label">Notes / Description</label>
                    <input type="text" name="description" id="expDesc" class="form-control" placeholder="e.g. 40 litres diesel">
                </div>
                
                <!-- Receipt attachment -->
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-paperclip" style="margin-right:4px;"></i>Receipt / Bill (optional)</label>
                    <div class="tp-filedrop" id="dropzone" onclick="document.getElementById('expFile').click()">
                        <i class="fas fa-cloud-upload-alt" style="font-size:24px;color:var(--t3);display:block;margin-bottom:6px;"></i>
                        <span id="dropText" style="font-size:13px;color:var(--t2);">Click or drag a JPG / PNG / PDF here</span>
                    </div>
                    <input type="file" name="attachment" id="expFile" accept="image/*,.pdf" style="display:none;" onchange="updateDropText(this)">
                </div>
                
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-gold" style="flex:2; justify-content:center;"><i class="fa fa-save"></i> Save Expense</button>
                    <button type="button" class="btn btn-outline" style="flex:1; justify-content:center;" onclick="closeModal('expenseModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Drag & drop
const dz = document.getElementById('dropzone');
if (dz) {
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag-over'); });
    dz.addEventListener('dragleave', ()=> dz.classList.remove('drag-over'));
    dz.addEventListener('drop', e => {
        e.preventDefault(); dz.classList.remove('drag-over');
        const f = e.dataTransfer.files[0];
        if(f) { document.getElementById('expFile').files = e.dataTransfer.files; updateDropText({files:[f]}); }
    });
}

function updateDropText(input) {
    const f = input.files[0];
    document.getElementById('dropText').textContent = f ? '📎 ' + f.name : 'Click or drag a JPG / PNG / PDF here';
}

// Category buttons
let activeCategory = null;
function setCategory(cat, cc, cbg, el) {
    document.querySelectorAll('#catGrid .tp-cat-btn').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');
    document.getElementById('expType').value = cat;
    activeCategory = cat;

    const otherWrap = document.getElementById('otherCategoryWrap');
    const otherInput = document.getElementById('otherCategoryName');
    if (cat === 'Other') {
        otherWrap.style.display = 'block';
        otherInput.setAttribute('required', 'required');
        otherInput.value = '';
        document.getElementById('expType').value = '';
        otherInput.focus();
    } else {
        otherWrap.style.display = 'none';
        otherInput.removeAttribute('required');
        otherInput.value = '';
    }
}

function updateOtherType(val) {
    document.getElementById('expType').value = val;
}

// Modal open helpers
function openAddModal() {
    expReset();
    openModal('expenseModal');
}

// Edit
function expEdit(e) {
    document.getElementById('expFormTitle').innerHTML = '<i class="fas fa-edit" style="color:var(--gold);margin-right:6px;"></i>Edit Expense';
    document.getElementById('expId').value      = e.id;
    document.getElementById('expVehicle').value = e.vehicle_id;
    document.getElementById('expAmount').value  = e.amount;
    document.getElementById('expDate').value    = e.date;
    document.getElementById('expDesc').value    = e.description || '';
    document.getElementById('expType').value    = e.expense_type;

    const standardCats = ['Fuel', 'Servicing', 'Tyres', 'Insurance', 'Challan'];
    const otherWrap = document.getElementById('otherCategoryWrap');
    const otherInput = document.getElementById('otherCategoryName');

    document.querySelectorAll('#catGrid .tp-cat-btn').forEach(b => b.classList.remove('active'));

    if (standardCats.includes(e.expense_type)) {
        const btn = document.querySelector(`[data-cat="${e.expense_type}"]`);
        if (btn) btn.classList.add('active');
        otherWrap.style.display = 'none';
        otherInput.removeAttribute('required');
        otherInput.value = '';
    } else {
        const btn = document.querySelector(`[data-cat="Other"]`);
        if (btn) btn.classList.add('active');
        otherWrap.style.display = 'block';
        otherInput.setAttribute('required', 'required');
        otherInput.value = e.expense_type;
    }

    openModal('expenseModal');
}

function expReset() {
    document.getElementById('expFormTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--gold);margin-right:6px;"></i>Log Expense';
    document.getElementById('expId').value = '';
    document.getElementById('expForm').reset();
    document.getElementById('dropText').textContent = 'Click or drag a JPG / PNG / PDF here';
    document.querySelectorAll('#catGrid .tp-cat-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('otherCategoryWrap').style.display = 'none';
    document.getElementById('otherCategoryName').removeAttribute('required');
}

function filterLedger() {
    const q = document.getElementById('ledgerSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.exp-ledger-row');
    rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

function exportLedgerToCSV() {
    let csv = 'Category,Vehicle,Date,Amount,Description\n';
    const rows = document.querySelectorAll('.exp-ledger-row');
    if(rows.length === 0) {
        alert('No records to export');
        return;
    }
    rows.forEach(r => {
        const cat = r.querySelector('[style*="font-weight:700"]').textContent.trim();
        const subText = r.querySelector('[style*="font-size:12px"]').textContent.trim();
        const amount = r.querySelector('[style*="font-weight:800"]').textContent.replace('₹', '').replace(/,/g, '').trim();
        
        const parts = subText.split('·');
        const vehicle = parts[0] ? parts[0].trim() : '';
        const date = parts[1] ? parts[1].trim() : '';
        const desc = parts[2] ? parts[2].trim() : '';
        
        csv += `"${cat}","${vehicle}","${date}","${amount}","${desc.replace(/"/g, '""')}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', `vehicle-expenses-${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection

