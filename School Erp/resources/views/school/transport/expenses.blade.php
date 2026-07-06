@extends('layouts.app')

@section('page-title', 'Vehicle Expenses')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-wallet" style="color:var(--gold);margin-right:8px;"></i>Vehicle Expenses Ledger</h1>
        <p>Log and track fleet maintenance costs, fuel bills, insurance premiums, and transit staff salaries</p>
    </div>
</div>

<div class="grid-3">
    <!-- Form Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3 id="formTitle">Log Expense</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="POST" action="{{ route('school.transport.expenses') }}" id="expenseForm">
                @csrf
                <input type="hidden" name="id" id="expenseId">
                
                <div class="form-group">
                    <label class="form-label">Select Vehicle <span style="color:red;">*</span></label>
                    <select name="vehicle_id" id="expenseVehicle" class="form-control" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->vehicle_no }} ({{ $v->vehicle_model ?: 'Bus' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Expense Category / Type <span style="color:red;">*</span></label>
                    <select name="expense_type" id="expenseType" class="form-control" required>
                        <option value="Fuel">Fuel (Diesel/Petrol)</option>
                        <option value="Maintenance">Maintenance & Service</option>
                        <option value="Staff Salary">Driver / Conductor Salary</option>
                        <option value="Insurance">Insurance Renewal</option>
                        <option value="Permit & Tolls">Permits, Tolls & Taxes</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (INR) <span style="color:red;">*</span></label>
                    <input type="number" step="0.01" name="amount" id="expenseAmount" class="form-control" placeholder="0.00" min="0.01" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Expense Date <span style="color:red;">*</span></label>
                    <input type="date" name="date" id="expenseDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Remarks / Description</label>
                    <textarea name="description" id="expenseDescription" class="form-control" style="height:80px;" placeholder="e.g. Servicing or diesel receipt info..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-gold" style="flex: 1; justify-content: center;">
                        <i class="fa fa-save"></i> Save Expense
                    </button>
                    <button type="button" class="btn btn-outline" id="clearBtn" style="display:none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses List Table -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Expense Records Ledger</h3>
            <span style="font-size:12.5px; font-weight:700; color:#ef4444; background:#fef2f2; padding:4px 10px; border-radius:12px;">Total Ledger: ₹{{ number_format($expenses->sum('amount'), 2) }}</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px 18px; text-align:left;">#</th>
                            <th style="padding:12px 18px; text-align:left;">Date</th>
                            <th style="padding:12px 18px; text-align:left;">Bus No</th>
                            <th style="padding:12px 18px; text-align:left;">Category</th>
                            <th style="padding:12px 18px; text-align:left;">Description</th>
                            <th style="padding:12px 18px; text-align:right;">Amount</th>
                            <th style="padding:12px 18px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $index => $e)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:14px 18px;"><span class="row-index">{{ $index + 1 }}</span></td>
                                <td style="padding:14px 18px; color:#475569; font-weight:600;">{{ $e->date->format('d M Y') }}</td>
                                <td style="padding:14px 18px; font-weight:700; color:#2563eb;">{{ $e->vehicle?->vehicle_no }}</td>
                                <td style="padding:14px 18px; font-weight:600; color:#1e293b;">{{ $e->expense_type }}</td>
                                <td style="padding:14px 18px; color:#64748b; font-size:12.5px;">{{ $e->description ?: '—' }}</td>
                                <td style="padding:14px 18px; text-align:right; font-weight:700; color:#ef4444;">₹{{ number_format($e->amount, 2) }}</td>
                                <td style="padding:14px 18px; text-align:center; white-space:nowrap;">
                                    <button class="btn-action-edit" onclick="editExpense({{ json_encode($e) }})" title="Edit Expense">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('school.transport.delete') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this expense log?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $e->id }}">
                                        <input type="hidden" name="type" value="expense">
                                        <button type="submit" class="btn-action-delete" title="Delete Expense">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:30px; text-align:center; color:#64748b;">
                                    <i class="fas fa-wallet" style="font-size:24px; color:#cbd5e1; margin-bottom:10px; display:block;"></i>
                                    No expenses logged yet. Add one from the form.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function editExpense(expense) {
        document.getElementById('formTitle').innerText = 'Edit Expense Log';
        document.getElementById('expenseId').value = expense.id;
        document.getElementById('expenseVehicle').value = expense.vehicle_id;
        document.getElementById('expenseType').value = expense.expense_type;
        document.getElementById('expenseAmount').value = expense.amount;
        // Format date to YYYY-MM-DD
        let rawDate = new Date(expense.date);
        let formattedDate = rawDate.toISOString().split('T')[0];
        document.getElementById('expenseDate').value = formattedDate;
        document.getElementById('expenseDescription').value = expense.description || '';
        document.getElementById('clearBtn').style.display = 'inline-block';
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = 'Log Expense';
        document.getElementById('expenseId').value = '';
        document.getElementById('expenseForm').reset();
        document.getElementById('clearBtn').style.display = 'none';
    }
</script>
@endsection
