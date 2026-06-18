@extends('layouts.app')

@section('page-title', 'Student-wise Fee')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-user-graduate" style="color:var(--gold);margin-right:8px;"></i>Student-wise Fee & Collection</h1>
        <p>Monitor individual student fee accounts, record manual payments, and review outstanding dues</p>
    </div>
</div>

<div class="grid-3">
    <!-- Manual Fee Payment Collection Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Record Payment Collection</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.student-wise') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Student Outstanding Dues</label>
                    <select name="student_fee_id" class="form-control" required>
                        <option value="">Select Fee Record</option>
                        @foreach($fees->where('status', '!=', 'paid') as $f)
                            <option value="{{ $f->id }}">
                                {{ $f->student->full_name }} — {{ $f->category->name }} (₹{{ $f->amount - $f->paid_amount }} due)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount Received (₹)</label>
                    <input type="number" step="0.01" name="amount_paid" class="form-control" placeholder="e.g. 1500" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Mode</label>
                    <select name="payment_mode" class="form-control" required>
                        <option value="cash">Cash Payment</option>
                        <option value="cheque">Cheque Deposit</option>
                        <option value="online">Online UPI / Bank Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Transaction ID / Reference (Optional)</label>
                    <input type="text" name="transaction_id" class="form-control" placeholder="e.g. TXN9876543210">
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-indian-rupee-sign"></i> Collect Fee Payment
                </button>
            </form>
        </div>
    </div>

    <!-- Student Fees Register -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Student Fees Registry Accounts</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student Details</th>
                            <th>Fee Category</th>
                            <th>Fee Amount</th>
                            <th>Amount Paid</th>
                            <th>Due Status</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $f)
                        <tr>
                            <td>
                                <strong style="color:var(--navy);">{{ $f->student->full_name }}</strong>
                                <small style="display:block; color:var(--t2);">Class: {{ optional($f->student->class)->name ?? 'N/A' }} ({{ $f->student->admission_id }})</small>
                            </td>
                            <td>{{ $f->category->name }}</td>
                            <td><strong>₹{{ number_format($f->amount, 2) }}</strong></td>
                            <td><span style="color:var(--green); font-weight:700;">₹{{ number_format($f->paid_amount, 2) }}</span></td>
                            <td>
                                @if($f->status === 'paid')
                                    <span class="badge badge-success">Cleared</span>
                                @elseif($f->status === 'partially_paid')
                                    <span class="badge badge-warning">Partial Due</span>
                                @else
                                    <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td><span style="font-family:'Courier New', monospace;">{{ $f->due_date }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:20px; color:var(--t3);">No active student fee accounts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
