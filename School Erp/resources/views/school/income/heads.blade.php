@extends('layouts.app')

@section('title', 'Income Heads')

@section('styles')
<style>
/* ─── VARIABLES ──────────────────────────────── */
:root {
    --inc-green:      #10b981;
    --inc-green-dark: #047857;
    --inc-green-light:#ecfdf5;
    --inc-green-mid:  #059669;
    --inc-accent:    #34d399;
    --inc-white:     #ffffff;
    --inc-gray:      #f1f5f9;
    --inc-border:    #d1fae5;
    --inc-text:      #1e293b;
    --inc-text2:     #64748b;
    --inc-red:       #ef4444;
    --inc-blue:      #3b82f6;
    --inc-amber:     #f59e0b;
    --shadow-sm: 0 1px 3px rgba(16,185,129,.1);
    --shadow-md: 0 4px 16px rgba(16,185,129,.15);
    --shadow-lg: 0 12px 40px rgba(16,185,129,.2);
}

body.dark-mode {
    --inc-white:     #111827;
    --inc-gray:      #1f2937;
    --inc-border:    #1e293b;
    --inc-text:      #f8fafc;
    --inc-text2:     #94a3b8;
    --inc-green-light:rgba(16, 185, 129, 0.15);
}

/* ─── PAGE HEADER & CONTAINER ────────────────── */
.inc-container {
    padding: 24px;
    width: 100%;
}
.inc-breadcrumb {
    font-size: 12px;
    color: var(--inc-text2);
    margin-bottom: 12px;
}
.inc-breadcrumb a {
    color: var(--inc-green-mid);
    text-decoration: none;
}
.inc-card {
    background: var(--inc-white);
    border: 1.5px solid var(--inc-border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-top: 10px;
}
.inc-card-hdr {
    padding: 20px 24px;
    border-bottom: 1px solid var(--inc-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.inc-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--inc-text);
}
.inc-btn-green {
    background-color: var(--inc-green);
    color: #fff;
    border: none;
    padding: 8px 16px;
    font-size: 12.5px;
    font-weight: 700;
    border-radius: 30px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background-color 0.2s, transform 0.15s;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.inc-btn-green:hover {
    background-color: var(--inc-green-dark);
    transform: translateY(-1px);
}
.inc-btn-green:active {
    transform: translateY(0);
}

/* ─── TABLE STYLING ──────────────────────────── */
.inc-table-responsive {
    overflow-x: auto;
}
.inc-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.inc-table th {
    background: var(--inc-gray);
    color: var(--inc-text);
    font-weight: 700;
    padding: 14px 18px;
    border-bottom: 2px solid var(--inc-border);
    text-transform: uppercase;
    font-size: 11.5px;
    letter-spacing: 0.5px;
}
.inc-table td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--inc-border);
    color: var(--inc-text);
    vertical-align: middle;
}
.inc-table tr:hover {
    background: var(--inc-green-light);
}

.inc-more-btn {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: var(--inc-green-dark);
    color: #fff;
    border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 11px;
}

/* ─── ACTION BUTTONS ─────────────────────────── */
.inc-act-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    margin-right: 8px;
    transition: transform 0.15s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.inc-act-btn:hover {
    transform: scale(1.15);
}
.inc-act-btn.edit { color: var(--inc-amber); }
.inc-act-btn.delete { color: var(--inc-red); }

/* ─── MODALS ─────────────────────────────────── */
.inc-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.28s ease;
}
.inc-modal-overlay.open {
    opacity: 1; pointer-events: auto;
}
.inc-modal {
    background: var(--inc-white);
    border: 1.5px solid var(--inc-border);
    border-radius: 16px;
    width: 100%; max-width: 440px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.inc-modal-overlay.open .inc-modal {
    transform: translateY(0);
}
.inc-modal-hdr {
    background: var(--inc-green-mid);
    padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.inc-modal-hdr h3 {
    margin: 0; font-size: 16px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}
.modal-close {
    background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;
}
.inc-modal-body {
    padding: 24px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block; font-size: 12.5px; font-weight: 700;
    color: var(--inc-text); margin-bottom: 6px;
}
.form-group label span { color: var(--inc-red); }
.form-control {
    width: 100%; height: 38px; padding: 8px 12px;
    border: 1.5px solid var(--inc-border); border-radius: 8px;
    font-size: 13px; font-weight: 500; font-family: inherit;
    background: var(--inc-white); color: var(--inc-text);
    outline: none; transition: border-color 0.2s;
}
.form-control:focus {
    border-color: var(--inc-green-mid);
}
.modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    margin-top: 10px;
}
.inc-btn {
    padding: 8px 16px; font-size: 12.5px; font-weight: 700; border-radius: 8px;
    cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px;
}
.inc-btn-outline {
    background: transparent; color: var(--inc-text2); border: 1.5px solid var(--inc-border);
}
.inc-btn-outline:hover { background: var(--inc-gray); }
.inc-btn-primary {
    background: var(--inc-green); color: #fff;
}
.inc-btn-primary:hover { background: var(--inc-green-dark); }

/* ─── TOAST ──────────────────────────────────── */
#inc-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 2500;
    display: flex; flex-direction: column; gap: 10px;
}
.toast-msg {
    background: var(--inc-white); border: 1.5px solid var(--inc-border);
    padding: 12px 20px; border-radius: 10px; box-shadow: var(--shadow-md);
    display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600;
    animation: slideIn 0.3s forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>
@endsection

@section('content')
<div class="inc-container">
    <div class="inc-breadcrumb">
        <a href="{{ route('school.dashboard') }}">Home</a> / Income Account
    </div>

    <div class="inc-card">
        <div class="inc-card-hdr">
            <h2 class="inc-card-title">Income Account</h2>
            <button class="inc-btn-green" id="addHeadBtn">
                ADD NEW INCOME HEAD <i class="fas fa-plus-circle"></i>
            </button>
        </div>
        <div class="inc-card-body" style="padding: 0;">
            <div class="inc-table-responsive">
                <table class="inc-table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">More</th>
                            <th>Income Name</th>
                            <th>Company</th>
                            <th>Annual Budget Target</th>
                            <th style="width: 220px;">Budget Progress (Achieved)</th>
                            <th style="width: 140px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($heads as $head)
                        <tr id="row-{{ $head->id }}">
                            <td style="text-align: center;">
                                <button class="inc-more-btn"><i class="fas fa-plus"></i></button>
                            </td>
                            <td style="font-weight: 600;">{{ $head->name }}</td>
                            <td style="color: var(--inc-text2);">{{ $schoolName }}</td>
                            <td style="font-weight: 700; color: var(--inc-text);">₹{{ number_format($head->budget_target, 2) }}</td>
                            <td>
                                @if($head->budget_target > 0)
                                    @php
                                        $actual = $head->actual_revenue;
                                        $pct = min(100, round(($actual / $head->budget_target) * 100));
                                        $color = $pct < 35 ? '#ef4444' : ($pct < 75 ? '#f59e0b' : '#10b981');
                                    @endphp
                                    <div style="font-size:11.5px; margin-bottom:4px; display:flex; justify-content:space-between; font-weight:700;">
                                        <span style="color:{{ $color }};">{{ $pct }}% Achieved</span>
                                        <span style="color:var(--inc-text2);">₹{{ number_format($actual, 0) }} / ₹{{ number_format($head->budget_target, 0) }}</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                        <div style="width: {{ $pct }}%; height: 100%; background: {{ $color }}; border-radius: 3px;"></div>
                                    </div>
                                @else
                                    <span style="font-size:12px; color:var(--inc-text2); font-style:italic;">No target set</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="inc-act-btn edit" onclick="editHead({{ $head->id }}, '{{ addslashes($head->name) }}', {{ $head->budget_target }})" title="Edit">
                                    <i class="far fa-edit"></i>
                                </button>
                                <button class="inc-act-btn delete" onclick="deleteHead({{ $head->id }})" title="Delete">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--inc-text2); padding: 30px;">
                                No income heads found. Click the button above to add one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ADD / EDIT MODAL --}}
<div class="inc-modal-overlay" id="headModal">
    <div class="inc-modal">
        <div class="inc-modal-hdr">
            <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Add Income Head</h3>
            <button class="modal-close" id="modalClose"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="inc-modal-body">
            <form id="headForm">
                @csrf
                <input type="hidden" id="headId" name="head_id">
                <div class="form-group">
                    <label>Income Name <span>*</span></label>
                    <input type="text" class="form-control" name="name" id="fName" placeholder="e.g. Uniform Sales, Book Store, Donations" required>
                </div>
                <div class="form-group">
                    <label>Annual Budget Target (₹)</label>
                    <input type="number" class="form-control" name="budget_target" id="fBudget" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="modal-footer">
                    <button type="button" class="inc-btn inc-btn-outline" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="inc-btn inc-btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> <span>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TOAST SYSTEM --}}
<div id="inc-toast"></div>
@endsection

@section('scripts')
<script>
const modal      = document.getElementById('headModal');
const form       = document.getElementById('headForm');
const modalTitle = document.getElementById('modalTitle');
const headIdInput= document.getElementById('headId');
const nameInput  = document.getElementById('fName');

function openModal() { modal.classList.add('open'); }
function closeModal() { modal.classList.remove('open'); form.reset(); headIdInput.value = ''; }

document.getElementById('addHeadBtn').addEventListener('click', () => {
    modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Add Income Head';
    openModal();
});
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

function editHead(id, name, budget) {
    headIdInput.value = id;
    nameInput.value = name;
    document.getElementById('fBudget').value = budget || '0.00';
    modalTitle.innerHTML = '<i class="fas fa-pen"></i> Edit Income Head';
    openModal();
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = headIdInput.value;
    const url = id 
        ? '{{ url("school/income/heads") }}/' + id
        : '{{ route("school.income.heads.store") }}';
    const method = id ? 'PUT' : 'POST';

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.querySelector('span').textContent = 'Saving...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ 
                name: nameInput.value, 
                budget_target: document.getElementById('fBudget').value || '0.00',
                _method: method 
            })
        });
        const json = await res.json();
        if (json.success || res.ok) {
            showToast(json.message || 'Saved successfully.', 'success');
            closeModal();
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(json.message || 'Error saving.', 'error');
        }
    } catch(err) {
        showToast('Network error or duplicate value. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.querySelector('span').textContent = 'Save';
    }
});

async function deleteHead(id) {
    if (!confirm('Delete this income head? Associated vouchers might block this operation.')) return;
    try {
        const res = await fetch('{{ url("school/income/heads") }}/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) {
            showToast(json.message, 'success');
            const row = document.getElementById('row-' + id);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                row.style.transition = '.3s';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showToast(json.message || 'Error deleting.', 'error');
        }
    } catch(err) {
        showToast('Cannot delete. Head might contain linked vouchers.', 'error');
    }
}

function showToast(msg, type = 'success') {
    const toast = document.getElementById('inc-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + type;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
</script>
@endsection
