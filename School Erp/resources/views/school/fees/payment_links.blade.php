@extends('layouts.app')

@section('page-title', 'Payment Links')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-link" style="color:var(--gold);margin-right:8px;"></i>Payment Links Registry</h1>
        <p>Generate secure, short online payment links to share with parents via WhatsApp, SMS, or Email</p>
    </div>
</div>

<div class="grid-3">
    <!-- Generate Link Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Generate New Link</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.payment-links') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Student Details</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fee Amount (₹)</label>
                    <input type="number" name="amount" class="form-control" placeholder="e.g. 2800" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Purpose / Description</label>
                    <input type="text" name="purpose" class="form-control" placeholder="e.g. Tuition Fees - June" required>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-paper-plane"></i> Generate Link
                </button>
            </form>
        </div>
    </div>

    <!-- Generated Links Directory -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Active Payment Links History</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Purpose</th>
                            <th>Amount</th>
                            <th>Link URL</th>
                            <th>Link Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($links as $lnk)
                        <tr>
                            <td>
                                <strong style="color:var(--navy);">{{ $lnk->student->full_name }}</strong>
                                <small style="display:block; color:var(--t3);">{{ $lnk->student->admission_id }}</small>
                            </td>
                            <td>{{ $lnk->purpose }}</td>
                            <td><strong>₹{{ number_format($lnk->amount, 2) }}</strong></td>
                            <td>
                                <a href="{{ $lnk->link_url }}" target="_blank" style="color:var(--gold); font-size:11.5px; text-decoration:none;">
                                    <i class="fas fa-external-link-alt"></i> {{ substr($lnk->link_url, 0, 30) }}...
                                </a>
                            </td>
                            <td>
                                @if($lnk->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($lnk->status === 'paid')
                                    <span class="badge badge-blue">Settled</span>
                                @else
                                    <span class="badge badge-danger">Expired</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px; color:var(--t3);">No active payment links logged.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
