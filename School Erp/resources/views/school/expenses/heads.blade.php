@extends('layouts.app')

@section('title', 'Expense Heads')

@section('styles')
<style>
/* ─── VARIABLES ──────────────────────────────── */
:root {
    --exp-blue:      #1d4ed8;
    --exp-blue-dark: #1e3a8a;
    --exp-blue-light:#eff6ff;
    --exp-blue-mid:  #3b82f6;
    --exp-accent:    #60a5fa;
    --exp-white:     #ffffff;
    --exp-gray:      #f1f5f9;
    --exp-border:    #dbeafe;
    --exp-text:      #1e293b;
    --exp-text2:     #64748b;
    --exp-red:       #ef4444;
    --exp-green:     #10b981;
    --exp-green-hover: #0d9488;
    --exp-amber:     #f59e0b;
    --shadow-sm: 0 1px 3px rgba(29,78,216,.1);
    --shadow-md: 0 4px 16px rgba(29,78,216,.15);
    --shadow-lg: 0 12px 40px rgba(29,78,216,.2);
}

body.dark-mode {
    --exp-white:     #111827;
    --exp-gray:      #1f2937;
    --exp-border:    #1e293b;
    --exp-text:      #f8fafc;
    --exp-text2:     #94a3b8;
    --exp-blue-light:rgba(29, 78, 216, 0.15);
}

/* ─── PAGE HEADER & CONTAINER ────────────────── */
.exp-container {
    padding: 24px;
    width: 100%;
}
.exp-breadcrumb {
    font-size: 12px;
    color: var(--exp-text2);
    margin-bottom: 12px;
}
.exp-breadcrumb a {
    color: var(--exp-blue-mid);
    text-decoration: none;
}
.exp-card {
    background: var(--exp-white);
    border: 1.5px solid var(--exp-border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-top: 10px;
}
.exp-card-hdr {
    padding: 20px 24px;
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.exp-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--exp-text);
}
.exp-btn-green {
    background-color: var(--exp-green);
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
.exp-btn-green:hover {
    background-color: #059669;
    transform: translateY(-1px);
}
.exp-btn-green:active {
    transform: translateY(0);
}

/* ─── TABLE STYLING ──────────────────────────── */
.exp-table-responsive {
    overflow-x: auto;
}
.exp-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.exp-table th {
    background: var(--exp-gray);
    color: var(--exp-text);
    font-weight: 700;
    padding: 14px 18px;
    border-bottom: 2px solid var(--exp-border);
    text-transform: uppercase;
    font-size: 11.5px;
    letter-spacing: 0.5px;
}
.exp-table td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--exp-border);
    color: var(--exp-text);
    vertical-align: middle;
}
.exp-table tr:hover {
    background: var(--exp-blue-light);
}

.exp-more-btn {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: var(--exp-blue-dark);
    color: #fff;
    border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 11px;
}

/* ─── ACTION BUTTONS ─────────────────────────── */
.exp-act-btn {
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
.exp-act-btn:hover {
    transform: scale(1.15);
}
.exp-act-btn.edit { color: var(--exp-amber); }
.exp-act-btn.delete { color: var(--exp-red); }

/* ─── MODALS ─────────────────────────────────── */
.exp-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.28s ease;
}
.exp-modal-overlay.open {
    opacity: 1; pointer-events: auto;
}
.exp-modal {
    background: var(--exp-white);
    border: 1.5px solid var(--exp-border);
    border-radius: 16px;
    width: 100%; max-width: 440px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.exp-modal-overlay.open .exp-modal {
    transform: translateY(0);
}
.exp-modal-hdr {
    background: var(--exp-green);
    padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
    color: #fff;
}
.exp-modal-hdr h3 {
    margin: 0; font-size: 16px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}
.modal-close {
    background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;
}
.exp-modal-body {
    padding: 24px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block; font-size: 12.5px; font-weight: 700;
    color: var(--exp-text); margin-bottom: 6px;
}
.form-group label span { color: var(--exp-red); }
.form-control {
    width: 100%; height: 38px; padding: 8px 12px;
    border: 1.5px solid var(--exp-border); border-radius: 8px;
    font-size: 13px; font-weight: 500; font-family: inherit;
    background: var(--exp-white); color: var(--exp-text);
    outline: none; transition: border-color 0.2s;
}
.form-control:focus {
    border-color: var(--exp-blue-mid);
}
.modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    margin-top: 10px;
}
.exp-btn {
    padding: 8px 16px; font-size: 12.5px; font-weight: 700; border-radius: 8px;
    cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px;
}
.exp-btn-outline {
    background: transparent; color: var(--exp-text2); border: 1.5px solid var(--exp-border);
}
.exp-btn-outline:hover { background: var(--exp-gray); }
.exp-btn-primary {
    background: var(--exp-blue); color: #fff;
}
.exp-btn-primary:hover { background: var(--exp-blue-dark); }

/* ─── TOAST ──────────────────────────────────── */
#exp-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 2500;
    display: flex; flex-direction: column; gap: 10px;
}
.toast-msg {
    background: var(--exp-white); border: 1.5px solid var(--exp-border);
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
<div class="exp-container">
    <div class="exp-breadcrumb">
        <a href="{{ route('school.dashboard') }}">Home</a> / Expense Account
    </div>

    <div class="exp-card">
        <div class="exp-card-hdr">
            <h2 class="exp-card-title">Expense Account</h2>
            <button class="exp-btn-green" id="addHeadBtn">
                ADD NEW EXPENSE HEAD <i class="fas fa-plus-circle"></i>
            </button>
        </div>
        <div class="exp-card-body" style="padding: 0;">
            <div class="exp-table-responsive">
                <table class="exp-table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">More</th>
                            <th>Expense Name</th>
                            <th>Company</th>
                            <th style="width: 140px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($heads as $head)
                        <tr id="row-{{ $head->id }}">
                            <td style="text-align: center;">
                                <button class="exp-more-btn"><i class="fas fa-plus"></i></button>
                            </td>
                            <td style="font-weight: 600;">{{ $head->name }}</td>
                            <td style="color: var(--exp-text2);">{{ $schoolName }}</td>
                            <td style="text-align: center;">
                                <button class="exp-act-btn edit" onclick="editHead({{ $head->id }}, '{{ addslashes($head->name) }}')" title="Edit">
                                    <i class="far fa-edit"></i>
                                </button>
                                <button class="exp-act-btn delete" onclick="deleteHead({{ $head->id }})" title="Delete">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--exp-text2); padding: 30px;">
                                No expense heads found. Click the button above to add one.
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
<div class="exp-modal-overlay" id="headModal">
    <div class="exp-modal">
        <div class="exp-modal-hdr">
            <h3 id="modalTitle"><i class="fas fa-plus-circle"></i> Add Expense Head</h3>
            <button class="modal-close" id="modalClose"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="exp-modal-body">
            <form id="headForm">
                @csrf
                <input type="hidden" id="headId" name="head_id">
                <div class="form-group">
                    <label>Expense Name <span>*</span></label>
                    <input type="text" class="form-control" name="name" id="fName" placeholder="e.g. CNG, Diesel, Internet Bill" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="exp-btn exp-btn-outline" id="modalCancelBtn">Cancel</button>
                    <button type="submit" class="exp-btn exp-btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> <span>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TOAST SYSTEM --}}
<div id="exp-toast"></div>
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
    modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Add Expense Head';
    openModal();
});
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancelBtn').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

function editHead(id, name) {
    headIdInput.value = id;
    nameInput.value = name;
    modalTitle.innerHTML = '<i class="fas fa-pen"></i> Edit Expense Head';
    openModal();
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = headIdInput.value;
    const url = id 
        ? '{{ url("school/expenses/heads") }}/' + id
        : '{{ route("school.expenses.heads.store") }}';
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
    if (!confirm('Delete this expense head? Associated vouchers might block this operation.')) return;
    try {
        const res = await fetch('{{ url("school/expenses/heads") }}/' + id, {
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
    const toast = document.getElementById('exp-toast');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + type;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="color:${type==='success'?'#10b981':'#ef4444'}"></i> ${msg}`;
    toast.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
</script>
@endsection
