@extends('layouts.app')

@section('page-title', 'Redesigned Fee Invoices')

@section('styles')
<style>
    /* Redesigned Invoices Page - Premium High-End Aesthetic */
    :root {
        --inv-primary: #1e3a8a;
        --inv-primary-light: #eff6ff;
        --inv-accent: #d97706;
        --inv-dark: #0f172a;
        --inv-border: #cbd5e1;
        --inv-green: #16a34a;
        --inv-red: #dc2626;
        --inv-purple: #7e22ce;
    }

    .inv-header {
        background: linear-gradient(135deg, var(--inv-primary) 0%, #1d4ed8 100%);
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 4px 20px rgba(30, 58, 138, 0.15);
    }
    .inv-header h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .inv-header p {
        color: #93c5fd;
        font-size: 0.88rem;
        margin: 0;
    }

    /* Class Selection Selector */
    .class-selector-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--inv-border);
        padding: 20px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .class-selector-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 250px;
    }
    .class-selector-group label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--inv-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .class-select-dropdown {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--inv-dark);
        border: 1.5px solid var(--inv-border);
        border-radius: 8px;
        padding: 8px 12px;
        background: #f8faff;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .class-select-dropdown:focus {
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }

    /* Student Table Card */
    .inv-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--inv-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .inv-card-hdr {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .inv-card-hdr h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--inv-dark);
        margin: 0;
    }
    .inv-search-wrap {
        position: relative;
        min-width: 280px;
    }
    .inv-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .inv-search-input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border-radius: 8px;
        border: 1.5px solid var(--inv-border);
        outline: none;
        font-size: 0.88rem;
        font-weight: 600;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .inv-search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Table styles */
    .inv-table {
        width: 100%;
        border-collapse: collapse;
    }
    .inv-table th {
        background: #f8fafc;
        padding: 14px 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        text-align: left;
    }
    .inv-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: var(--inv-dark);
    }
    .inv-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .inv-student-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .inv-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1d4ed8, #0284c7);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
    }
    .inv-student-name {
        font-weight: 700;
        color: var(--inv-dark);
    }
    .inv-student-class {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 1px;
    }

    /* Badges */
    .inv-badge {
        font-size: 0.72rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-transform: uppercase;
    }
    .inv-badge.pending { background: #fef3c7; color: #d97706; border-color: #fde68a; }
    .inv-badge.paid { background: #dcfce7; color: #16a34a; border-color: #bbf7d0; }
    .inv-badge.partial { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .inv-badge.refunded { background: #f3e8ff; color: #7e22ce; border-color: #e9d5ff; }
    .inv-badge.cancelled { background: #fef2f2; color: #991b1b; border-color: #ef4444; font-weight: 800; }

    /* Action Buttons */
    .inv-btn-eye {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .inv-btn-eye:hover {
        background: #1d4ed8;
        color: white;
        border-color: #1d4ed8;
        transform: scale(1.03);
    }

    /* Empty Placeholder state */
    .inv-placeholder {
        padding: 60px 40px;
        text-align: center;
        color: #64748b;
    }
    .inv-placeholder i {
        font-size: 3rem;
        color: #bfdbfe;
        margin-bottom: 16px;
    }
    .inv-placeholder h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--inv-dark);
        margin: 0 0 6px 0;
    }
    .inv-placeholder p {
        font-size: 0.88rem;
        margin: 0;
    }

    /* Modal Styling */
    .inv-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .inv-modal-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }
    .inv-modal {
        background: white;
        width: 100%;
        max-width: 850px;
        max-height: 90vh;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: translateY(20px);
        transition: transform 0.25s ease;
    }
    .inv-modal-overlay.open .inv-modal {
        transform: translateY(0);
    }
    .inv-modal-hdr {
        background: var(--inv-primary);
        color: white;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .inv-modal-hdr h3 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        margin: 0;
    }
    .inv-modal-close {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border: none;
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s;
    }
    .inv-modal-close:hover { background: rgba(255, 255, 255, 0.3); }

    .inv-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex-grow: 1;
    }

    /* Student Info Panel in Modal */
    .inv-student-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
        font-size: 0.88rem;
    }
    .inv-student-meta div span {
        font-weight: 700;
        color: #475569;
    }

    /* Installment Cards */
    .inv-inst-card {
        border: 1px solid var(--inv-border);
        border-radius: 12px;
        margin-bottom: 16px;
        overflow: hidden;
        background: white;
    }
    .inv-inst-hdr {
        background: #f8fafc;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }
    .inv-inst-title {
        font-weight: 800;
        color: var(--inv-primary);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .inv-inst-amounts {
        font-size: 0.82rem;
        color: #475569;
        font-weight: 600;
    }
    .inv-inst-actions {
        display: flex;
        gap: 8px;
    }

    /* Invoice buttons inside modal */
    .inv-btn-print {
        background: white;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.78rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }
    .inv-btn-print:hover {
        background: #3b82f6;
        color: white;
    }

    .inv-btn-cancel {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: var(--inv-red);
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.78rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }
    .inv-btn-cancel:hover {
        background: var(--inv-red);
        color: white;
        border-color: var(--inv-red);
    }

    /* Table inside installment card */
    .inv-inst-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.83rem;
    }
    .inv-inst-table th {
        background: white;
        color: #475569;
        font-weight: 700;
        padding: 8px 18px;
        border-bottom: 1px solid #f1f5f9;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .inv-inst-table td {
        padding: 10px 18px;
        border-bottom: 1px solid #f8fafc;
        color: #334155;
    }

    /* Loader */
    .inv-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        gap: 12px;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .inv-spinner {
        width: 32px; height: 32px;
        border: 3.5px solid #cbd5e1;
        border-top-color: var(--inv-primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive adjustments for premium high-end feel */
    @media (max-width: 992px) {
        .inv-modal {
            width: calc(100% - 32px);
            margin: 16px;
            max-height: 92vh;
        }
        .inv-inst-hdr {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
        }
        .inv-inst-actions {
            width: 100%;
            justify-content: flex-start;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .class-selector-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .class-selector-group {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 600px) {
        .inv-student-meta {
            grid-template-columns: 1fr;
            gap: 8px;
            padding: 12px;
        }
        .inv-search-wrap {
            width: 100%;
            min-width: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="inv-header">
    <h1><i class="fas fa-file-invoice"></i> Student Invoice Manager</h1>
    <p>View, print, and cancel standardized fee invoices on a student-wise breakdown with high-speed performance.</p>
</div>

<!-- Class Selection Card -->
<div class="class-selector-card">
    <div class="class-selector-group">
        <label>Select School Class</label>
        <select id="classFilterSelect" class="class-select-dropdown" onchange="loadClassStudents(this.value)">
            <option value="">-- Choose Class --</option>
            @foreach($classes as $cls)
                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
            @endforeach
        </select>
    </div>
    <div style="flex-grow:1; font-size: 0.88rem; color: #475569; font-weight:600;">
        Filter students by class to view, print, or cancel installments and invoices.
    </div>
</div>

<div class="inv-card" id="studentListCard" style="display:none;">
    <div class="inv-card-hdr">
        <h3 id="classTitleText">Students List</h3>
        <div class="inv-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="studentSearch" class="inv-search-input" placeholder="Search in this class...">
        </div>
    </div>
    
    <div style="overflow-x:auto;">
        <table class="inv-table" id="studentsTable">
            <thead>
                <tr>
                    <th>Admission No</th>
                    <th>Student Name</th>
                    <th>Father's Name</th>
                    <th>Class & Section</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <!-- Dynamically populated via JS -->
            </tbody>
        </table>
    </div>
</div>

{{-- Placeholder State --}}
<div class="inv-card" id="emptyPlaceholder">
    <div class="inv-placeholder">
        <i class="fas fa-graduation-cap"></i>
        <h4>No Class Selected</h4>
        <p>Please select a class from the dropdown above to view students and manage their invoices.</p>
    </div>
</div>

{{-- MODERN DETAILED INVOICE MODAL --}}
<div class="inv-modal-overlay" id="invoiceModal" onclick="if(event.target===this) closeModal()">
    <div class="inv-modal">
        <div class="inv-modal-hdr">
            <h3 id="modalStudentTitle"><i class="fas fa-file-invoice"></i> Student Invoices</h3>
            <button class="inv-modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="inv-modal-body">
            <!-- Student Header Info -->
            <div class="inv-student-meta">
                <div><span>Student Name:</span> <span id="metaStudentName" style="color:var(--inv-primary)"></span></div>
                <div><span>Class & Section:</span> <span id="metaStudentClass"></span></div>
                <div><span>Admission Number:</span> <span id="metaStudentID" style="font-weight:700"></span></div>
                <div><span>Father's Name:</span> <span id="metaStudentFather"></span></div>
            </div>

            <!-- Invoices Container / Loader -->
            <div id="invoicesList">
                <!-- Invoices list loaded dynamically via JS -->
            </div>
        </div>
    </div>
</div>

<script>
    // Comprehensive student dataset from Laravel
    const allStudents = [
        @foreach($students as $st)
        {
            id: {{ $st->id }},
            name: "{{ $st->full_name }}",
            admission_no: "{{ $st->admission_number }}",
            father_name: "{{ $st->father_name ?? '—' }}",
            class_id: {{ $st->class_id ?? 'null' }},
            class_name: "{{ optional($st->class)->name ?? 'N/A' }}",
            section_name: "{{ optional($st->section)->name ?? 'N/A' }}"
        },
        @endforeach
    ];

    // Load and filter students based on selected Class
    function loadClassStudents(classId) {
        const placeholder = document.getElementById('emptyPlaceholder');
        const listCard = document.getElementById('studentListCard');
        const tbody = document.getElementById('studentTableBody');
        const searchInput = document.getElementById('studentSearch');
        const selectElement = document.getElementById('classFilterSelect');

        // Reset search field
        searchInput.value = '';

        if (!classId) {
            placeholder.style.display = 'block';
            listCard.style.display = 'none';
            return;
        }

        const className = selectElement.options[selectElement.selectedIndex].text;
        document.getElementById('classTitleText').textContent = `Students in ${className}`;

        // Filter student list
        const filtered = allStudents.filter(st => st.class_id == classId);

        let html = '';
        filtered.forEach(st => {
            html += `
                <tr>
                    <td style="font-weight: 700; color: var(--inv-primary);">${st.admission_no}</td>
                    <td>
                        <div class="inv-student-profile">
                            <div class="inv-avatar">
                                ${st.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="inv-student-name">${st.name}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight: 600; color: #475569;">${st.father_name}</td>
                    <td>
                        <span class="badge badge-info" style="font-size: 0.8rem; font-weight: 700; background:#f0fdfa; color:#0d9488; border:1px solid #ccfbf1; padding:3px 8px; border-radius:6px;">
                            ${st.class_name} - ${st.section_name}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button class="inv-btn-eye" onclick="viewInvoices(${st.id})">
                            <i class="fas fa-eye"></i> View Invoices
                        </button>
                    </td>
                </tr>
            `;
        });

        if (filtered.length === 0) {
            html = `<tr><td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">No student records found in this class.</td></tr>`;
        }

        tbody.innerHTML = html;
        placeholder.style.display = 'none';
        listCard.style.display = 'block';
    }

    // Realtime Client-Side Search within filtered Class list
    document.getElementById('studentSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#studentTableBody tr');
        
        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            if (cells.length < 4) return;
            
            const admId = cells[0].textContent.toLowerCase();
            const name = cells[1].textContent.toLowerCase();
            const father = cells[2].textContent.toLowerCase();
            const cls = cells[3].textContent.toLowerCase();
            
            if (admId.includes(query) || name.includes(query) || father.includes(query) || cls.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // View Invoices Modal Loader
    function viewInvoices(studentId) {
        const overlay = document.getElementById('invoiceModal');
        const listContainer = document.getElementById('invoicesList');
        
        overlay.classList.add('open');
        listContainer.innerHTML = `
            <div class="inv-loader">
                <div class="inv-spinner"></div>
                Retrieving invoices list...
            </div>
        `;

        fetch(`/school/fees/student-invoices/${studentId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('metaStudentName').textContent = data.student.name;
                    document.getElementById('metaStudentClass').textContent = `${data.student.class} - ${data.student.section}`;
                    document.getElementById('metaStudentID').textContent = data.student.admission_number;
                    document.getElementById('metaStudentFather').textContent = data.student.father_name;

                    let html = '';
                    data.invoices.forEach(inv => {
                        const statusClass = getStatusClass(inv.status);
                        const isCancelled = inv.status === 'cancelled';
                        const isTransport = inv.is_transport === true;
                        const transportBadge = isTransport 
                            ? `<span style="font-size:0.68rem; background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; border-radius:4px; padding:1px 7px; font-weight:700; margin-left:6px;"><i class="fas fa-bus" style="margin-right:3px;"></i>Transport</span>`
                            : '';
                        
                        html += `
                            <div class="inv-inst-card" style="${isTransport ? 'border-left: 3px solid #16a34a;' : ''}">
                                <div class="inv-inst-hdr">
                                    <div class="inv-inst-title">
                                        <span>${inv.installment_label || ('Installment ' + inv.installment_no)}</span>
                                        ${transportBadge}
                                        <span class="inv-badge ${statusClass}">${inv.status.replace('_', ' ')}</span>
                                    </div>
                                    <div class="inv-inst-amounts">
                                        Total: ₹${Number(inv.total).toFixed(0)} &nbsp;|&nbsp; 
                                        Discount: <span style="color:var(--inv-red)">₹${Number(inv.discount).toFixed(0)}</span> &nbsp;|&nbsp; 
                                        Paid: <span style="color:var(--inv-green)">₹${Number(inv.paid).toFixed(0)}</span> &nbsp;|&nbsp; 
                                        Outstanding: <span style="color:var(--inv-primary)">₹${Number(inv.due).toFixed(0)}</span>
                                    </div>
                                    <div class="inv-inst-actions">
                                        <a href="/school/fees/print-slip/invoice/${inv.invoice_no}?student_id=${data.student.id}" 
                                           target="_blank" 
                                           class="inv-btn-print">
                                            <i class="fas fa-print"></i> Print Invoice
                                        </a>
                                        ${!(isCancelled || inv.status === 'refunded' || inv.status === 'bounced' || inv.invoice_status === 'bounced') ? `
                                            <button class="inv-btn-cancel" onclick="cancelInvoiceAjax('${inv.invoice_no}', ${inv.installment_no}, ${data.student.id})">
                                                <i class="fas fa-ban"></i> Cancel Invoice
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                                <div style="overflow-x:auto;">
                                    <table class="inv-inst-table">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left;">Fee Component</th>
                                                <th style="text-align:right;">Original Amount</th>
                                                <th style="text-align:right;">Discount</th>
                                                <th style="text-align:right;">Paid</th>
                                                <th style="text-align:right;">Outstanding</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${inv.components.map(comp => `
                                                <tr style="${comp.status === 'refunded' ? 'background:#faf5ff;' : (comp.is_transport ? 'background:#f0fdf4;' : '')}">
                                                    <td style="font-weight:600; text-align:left;">
                                                        ${comp.name}
                                                        ${comp.status === 'refunded' ? '<span style="font-size:0.65rem; background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff; border-radius:4px; padding:1px 4px; margin-left:6px; font-weight:700;"><i class="fas fa-undo"></i> Refunded</span>' : ''}
                                                    </td>
                                                    <td style="text-align:right; font-weight:600;">₹${Number(comp.amount).toFixed(2)}</td>
                                                    <td style="text-align:right; color:var(--inv-red); font-weight:600;">₹${Number(comp.discount).toFixed(2)}</td>
                                                    <td style="text-align:right; color:var(--inv-green); font-weight:600;">₹${Number(comp.paid).toFixed(2)}</td>
                                                    <td style="text-align:right; color:var(--inv-primary); font-weight:700;">₹${Number(comp.due).toFixed(2)}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    });

                    if (data.invoices.length === 0) {
                        html = `
                            <div style="text-align:center; padding:30px; color:#94a3b8; font-weight:600;">
                                <i class="fas fa-info-circle" style="font-size:1.5rem; margin-bottom:8px; display:block;"></i>
                                No invoices or installments found for this student.
                            </div>
                        `;
                    }

                    listContainer.innerHTML = html;
                } else {
                    listContainer.innerHTML = `<div style="color:var(--inv-red); font-weight:700; text-align:center; padding:20px;">Could not retrieve invoices.</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                listContainer.innerHTML = `<div style="color:var(--inv-red); font-weight:700; text-align:center; padding:20px;">Network error loading invoices.</div>`;
            });
    }

    // Cancel Invoice dynamically via AJAX
    function cancelInvoiceAjax(invoiceNo, installmentNo, studentId) {
        const remarks = prompt('Please enter the reason for cancelling this invoice:');
        if (remarks === null) return;
        if (!remarks.trim()) {
            alert('Cancellation reason is required.');
            return;
        }

        const btn = event.currentTarget || document.activeElement;
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Cancelling...`;
        }

        fetch(`/school/fees/student-wise`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: 'cancel_invoice',
                student_id: studentId,
                installment_no: installmentNo,
                invoice_no: invoiceNo,
                remarks: remarks
            })
        })
        .then(res => {
            if (res.ok) {
                alert('Invoice cancelled successfully and installment fees restored!');
                viewInvoices(studentId);
            } else {
                alert('Error cancelling invoice. Please try again.');
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to connect to the server.');
            btn.disabled = false;
            btn.innerHTML = oldHtml;
        });
    }

    function closeModal() {
        document.getElementById('invoiceModal').classList.remove('open');
    }

    function getStatusClass(status) {
        status = status.toLowerCase();
        if (status === 'paid') return 'paid';
        if (status === 'partially_paid' || status === 'partial') return 'partial';
        if (status === 'refunded') return 'refunded';
        if (status === 'cancelled') return 'cancelled';
        return 'pending';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const studentId = urlParams.get('student_id');
        if (studentId) {
            const student = allStudents.find(st => st.id == studentId);
            if (student) {
                const selectElement = document.getElementById('classFilterSelect');
                if (selectElement && student.class_id) {
                    selectElement.value = student.class_id;
                    loadClassStudents(student.class_id);
                }
                viewInvoices(student.id);
            }
        }
    });
</script>
@endsection
