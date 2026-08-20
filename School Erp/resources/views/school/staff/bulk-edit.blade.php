@extends('layouts.app')

@section('page-title', 'Staff Bulk Edit')

@section('styles')
<style>
    .excel-container {
        background: var(--card, #ffffff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .excel-toolbar {
        background: #f8fafc;
        border-bottom: 1px solid var(--border, #e2e8f0);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .excel-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .excel-title-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #107c41;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 10px rgba(16, 124, 65, 0.25);
    }
    .excel-title h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--t1, #1e293b);
    }
    .excel-title p {
        margin: 2px 0 0 0;
        font-size: 12px;
        color: var(--t3, #64748b);
    }

    .excel-filters {
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--border, #e2e8f0);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .excel-filters .form-control, .excel-filters .form-select {
        height: 38px;
        font-size: 13px;
        border-radius: 8px;
    }

    /* Spreadsheet Grid Styles */
    .excel-table-wrap {
        max-height: 70vh;
        overflow: auto;
        position: relative;
    }
    .excel-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12.5px;
        background: #ffffff;
    }
    .excel-table th {
        position: sticky;
        top: 0;
        background: #107c41;
        color: #ffffff;
        font-weight: 600;
        text-align: left;
        padding: 10px 12px;
        white-space: nowrap;
        border-right: 1px solid rgba(255, 255, 255, 0.15);
        border-bottom: 2px solid #0b592e;
        z-index: 10;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .excel-table th.col-num {
        background: #0d6535;
        text-align: center;
        width: 50px;
        min-width: 50px;
        position: sticky;
        left: 0;
        z-index: 20;
    }
    .excel-table td {
        padding: 0;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        vertical-align: middle;
        position: relative;
    }
    .excel-table td.col-num {
        position: sticky;
        left: 0;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 700;
        text-align: center;
        font-size: 11px;
        z-index: 5;
        border-right: 2px solid #cbd5e1;
    }
    .excel-table tr:hover td {
        background: #f8fafc;
    }
    .excel-table tr:hover td.col-num {
        background: #e2e8f0;
        color: #107c41;
    }

    /* Editable Input Cells */
    .cell-input {
        width: 100%;
        height: 38px;
        border: none;
        outline: none;
        padding: 6px 10px;
        font-size: 12.5px;
        background: transparent;
        color: #1e293b;
        font-family: inherit;
        transition: all 0.15s ease;
    }
    .cell-input:focus {
        background: #eff6ff !important;
        box-shadow: inset 0 0 0 2px #2563eb;
    }
    .cell-select {
        width: 100%;
        height: 38px;
        border: none;
        outline: none;
        padding: 6px 8px;
        font-size: 12.5px;
        background: transparent;
        color: #1e293b;
        font-family: inherit;
        cursor: pointer;
    }
    .cell-select:focus {
        background: #eff6ff !important;
        box-shadow: inset 0 0 0 2px #2563eb;
    }

    /* Modified Cell Indicator */
    .is-modified {
        background-color: #fef9c3 !important;
    }
    .is-modified .cell-input, .is-modified .cell-select {
        font-weight: 600;
    }
    .excel-table td.is-modified::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 7px 7px 0;
        border-color: transparent #d97706 transparent transparent;
    }

    /* Action Footer */
    .excel-footer {
        background: #f8fafc;
        border-top: 1px solid var(--border, #e2e8f0);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .status-badge-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .btn-excel-save {
        background: #107c41 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 10px 22px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 4px 12px rgba(16, 124, 65, 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .btn-excel-save:hover {
        background: #0d6535 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(16, 124, 65, 0.35) !important;
    }

    /* Toast alert banner */
    #ajaxToastAlert {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.18);
        border-radius: 10px;
        display: none;
    }

    body.dark-mode .excel-container {
        background: #1e293b;
        border-color: #334155;
    }
    body.dark-mode .excel-toolbar, body.dark-mode .excel-filters, body.dark-mode .excel-footer {
        background: #0f172a;
        border-color: #334155;
    }
    body.dark-mode .excel-table {
        background: #1e293b;
    }
    body.dark-mode .excel-table td {
        border-color: #334155;
        background: #1e293b;
    }
    body.dark-mode .excel-table td.col-num {
        background: #0f172a;
        color: #94a3b8;
        border-right-color: #334155;
    }
    body.dark-mode .cell-input, body.dark-mode .cell-select {
        color: #f8fafc;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Toast Notification Banner -->
    <div id="ajaxToastAlert" class="alert alert-success d-flex align-items-center mb-0 fade show" role="alert">
        <i class="fas fa-check-circle me-2 fs-5" id="toastIcon"></i>
        <span id="toastMsg" class="fw-bold">Changes saved successfully!</span>
    </div>

    <div class="excel-container">
        <!-- Toolbar Header -->
        <div class="excel-toolbar">
            <div class="excel-title">
                <div class="excel-title-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div>
                    <h2>Staff Bulk Edit Spreadsheet</h2>
                    <p>Fast Excel-style inline editing with zero-delay AJAX save</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('school.staff.index') }}" class="btn btn-outline-secondary" style="border-radius:8px; font-weight:600; font-size:13px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Directory
                </a>
                <button type="button" class="btn-excel-save" onclick="submitBulkEditForm()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="excel-filters">
            <form action="{{ route('school.staff.bulk-edit') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap w-100" id="filterForm">
                <div style="min-width: 170px;">
                    <select name="department_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 170px;">
                    <select name="designation_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Designations</option>
                        @foreach($designations as $desg)
                            <option value="{{ $desg->id }}" {{ $desgId == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 140px;">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                    </select>
                </div>

                <div class="flex-grow-1" style="min-width: 200px;">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, employee ID, phone..." value="{{ $search }}">
                </div>

                <button type="submit" class="btn btn-primary" style="height:38px; border-radius:8px; font-weight:600; font-size:13px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if($deptId || $desgId || $search || $status !== 'active')
                    <a href="{{ route('school.staff.bulk-edit') }}" class="btn btn-light" style="height:38px; border-radius:8px; font-weight:600; font-size:13px; border:1px solid #cbd5e1;">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Form for Bulk Save -->
        <form id="bulkEditForm" action="{{ route('school.staff.bulk-update') }}" method="POST">
            @csrf
            <div class="excel-table-wrap">
                <table class="excel-table">
                    <thead>
                        <tr>
                            <th class="col-num">#</th>
                            <th style="min-width: 120px;">Emp ID *</th>
                            <th style="min-width: 140px;">First Name *</th>
                            <th style="min-width: 140px;">Last Name</th>
                            <th style="min-width: 140px;">Department</th>
                            <th style="min-width: 140px;">Designation</th>
                            <th style="min-width: 160px;">Email</th>
                            <th style="min-width: 130px;">Phone</th>
                            <th style="min-width: 110px;">Gender</th>
                            <th style="min-width: 130px;">DOB</th>
                            <th style="min-width: 130px;">Joining Date</th>
                            <th style="min-width: 120px;">Basic Salary</th>
                            <th style="min-width: 140px;">Qualification</th>
                            <th style="min-width: 110px;">Experience (Yrs)</th>
                            <th style="min-width: 100px;">Blood Grp</th>
                            <th style="min-width: 130px;">PAN No</th>
                            <th style="min-width: 180px;">Address</th>
                            <th style="min-width: 110px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffList as $index => $stf)
                            <tr data-staff-id="{{ $stf->id }}">
                                <td class="col-num">{{ $index + 1 }}</td>

                                <td>
                                    <input type="text" data-field="employee_id" class="cell-input" value="{{ $stf->employee_id }}" required oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="text" data-field="first_name" class="cell-input" value="{{ $stf->first_name }}" required oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="text" data-field="last_name" class="cell-input" value="{{ $stf->last_name }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <select data-field="department_id" class="cell-select" onchange="markCellModified(this)">
                                        <option value="">-- None --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ $stf->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select data-field="designation_id" class="cell-select" onchange="markCellModified(this)">
                                        <option value="">-- None --</option>
                                        @foreach($designations as $desg)
                                            <option value="{{ $desg->id }}" {{ $stf->designation_id == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="email" data-field="email" class="cell-input" value="{{ $stf->email }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="text" data-field="phone" class="cell-input" value="{{ $stf->phone }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <select data-field="gender" class="cell-select" onchange="markCellModified(this)">
                                        <option value="">-- Select --</option>
                                        <option value="Male" {{ strtolower($stf->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ strtolower($stf->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ strtolower($stf->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" data-field="date_of_birth" class="cell-input" value="{{ $stf->date_of_birth ? \Carbon\Carbon::parse($stf->date_of_birth)->format('Y-m-d') : '' }}" onchange="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="date" data-field="joining_date" class="cell-input" value="{{ $stf->joining_date ? \Carbon\Carbon::parse($stf->joining_date)->format('Y-m-d') : '' }}" onchange="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="number" step="0.01" data-field="basic_salary" class="cell-input" value="{{ $stf->basic_salary }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="text" data-field="qualification" class="cell-input" value="{{ $stf->qualification }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="number" data-field="experience_years" class="cell-input" value="{{ $stf->experience_years }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <select data-field="blood_group" class="cell-select" onchange="markCellModified(this)">
                                        <option value="">-- None --</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                            <option value="{{ $bg }}" {{ $stf->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" data-field="pan_number" class="cell-input" value="{{ $stf->pan_number }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <input type="text" data-field="address" class="cell-input" value="{{ $stf->address }}" oninput="markCellModified(this)">
                                </td>
                                <td>
                                    <select data-field="is_active" class="cell-select" onchange="markCellModified(this)">
                                        <option value="1" {{ $stf->is_active ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$stf->is_active ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    No staff records found matching the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Bar -->
            <div class="excel-footer">
                <div class="status-badge-wrap">
                    <span class="badge bg-secondary" id="totalCountBadge">{{ count($staffList) }} Staff Members Loaded</span>
                    <span class="badge bg-warning text-dark d-none" id="modifiedCountBadge">0 fields modified</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-excel-save" onclick="submitBulkEditForm()">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const modifiedRows = new Set();
    let modifiedFieldsCount = 0;

    function markCellModified(element) {
        const td = element.closest('td');
        const tr = element.closest('tr');
        if (!td.classList.contains('is-modified')) {
            td.classList.add('is-modified');
            modifiedFieldsCount++;
        }
        if (tr && tr.dataset.staffId) {
            modifiedRows.add(tr);
        }
        updateModifiedBadge();
    }

    function updateModifiedBadge() {
        const badge = document.getElementById('modifiedCountBadge');
        if (modifiedFieldsCount > 0) {
            badge.textContent = `${modifiedFieldsCount} field(s) edited`;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function showToast(message, isError = false) {
        const alertEl = document.getElementById('ajaxToastAlert');
        const msgEl = document.getElementById('toastMsg');
        const iconEl = document.getElementById('toastIcon');

        msgEl.textContent = message;
        if (isError) {
            alertEl.className = "alert alert-danger d-flex align-items-center mb-0 fade show";
            iconEl.className = "fas fa-exclamation-circle me-2 fs-5";
        } else {
            alertEl.className = "alert alert-success d-flex align-items-center mb-0 fade show";
            iconEl.className = "fas fa-check-circle me-2 fs-5";
        }

        alertEl.style.display = 'flex';
        setTimeout(() => {
            alertEl.style.display = 'none';
        }, 4000);
    }

    async function submitBulkEditForm() {
        if (modifiedRows.size === 0) {
            showToast('No fields were changed to save.', false);
            return;
        }

        const btnBtns = document.querySelectorAll('.btn-excel-save');
        btnBtns.forEach(b => {
            b.disabled = true;
            b.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        });

        // Collect ONLY modified rows & fields into lightweight payload
        const staffData = {};
        modifiedRows.forEach(tr => {
            const staffId = tr.dataset.staffId;
            staffData[staffId] = {};
            tr.querySelectorAll('[data-field]').forEach(input => {
                staffData[staffId][input.dataset.field] = input.value;
            });
        });

        try {
            const response = await fetch('{{ route("school.staff.bulk-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ staff: staffData })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showToast(result.message || 'Saved successfully!');
                
                // Clear modified indicators
                document.querySelectorAll('.is-modified').forEach(td => td.classList.remove('is-modified'));
                modifiedRows.clear();
                modifiedFieldsCount = 0;
                updateModifiedBadge();
            } else {
                showToast(result.message || 'Failed to save changes.', true);
            }
        } catch (err) {
            showToast('Network error while saving changes.', true);
        } finally {
            btnBtns.forEach(b => {
                b.disabled = false;
                b.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
            });
        }
    }
</script>
@endsection
