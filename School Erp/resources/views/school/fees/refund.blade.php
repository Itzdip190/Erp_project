@extends('layouts.app')

@section('page-title', 'Refund Fee')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-undo-alt" style="color:var(--gold);margin-right:8px;"></i>Refund Fee Portal</h1>
        <p>Record, manage, and audit student fee refunds for cancellations or excessive deposits</p>
    </div>
</div>

<div class="grid-3">
    <!-- Refund Processing Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Record Refund</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.refund') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Refund Amount (₹)</label>
                    <input type="number" name="amount" class="form-control" placeholder="e.g. 1200" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Reason for Refund</label>
                    <input type="text" name="reason" class="form-control" placeholder="e.g. Double payment, Admission cancelled" required>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-undo"></i> Issue Refund
                </button>
            </form>
        </div>
    </div>

    <!-- Refund History List -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Fee Refunds History Audit</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student Details</th>
                            <th>Refund Amount</th>
                            <th>Refund Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $ref)
                        <tr>
                            <td>
                                <strong style="color:var(--navy);">{{ $ref->student->full_name }}</strong>
                                <small style="display:block; color:var(--t3);">{{ $ref->student->admission_id }}</small>
                            </td>
                            <td><strong style="color:var(--red);">₹{{ number_format($ref->amount, 2) }}</strong></td>
                            <td>{{ $ref->refund_date }}</td>
                            <td><span style="color:var(--t2);">{{ $ref->reason }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No refunds issued.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
