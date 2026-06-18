@extends('layouts.app')

@section('page-title', 'Pending Cheques')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-money-check" style="color:var(--gold);margin-right:8px;"></i>Pending Cheques Registry</h1>
        <p>Monitor offline cheques deposited by students/parents, record clearances, and handle bounced cheque logs</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Offline Cheques Register</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Bank Name</th>
                        <th>Cheque Number</th>
                        <th>Amount (₹)</th>
                        <th>Cheque Date</th>
                        <th>Cheque Status</th>
                        <th>Mark Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cheques as $chq)
                    <tr>
                        <td>
                            <strong style="color:var(--navy);">{{ $chq->student->full_name }}</strong>
                            <small style="display:block; color:var(--t3);">{{ $chq->student->admission_id }}</small>
                        </td>
                        <td>{{ $chq->bank_name }}</td>
                        <td><strong style="color:var(--navy); font-family:monospace;">{{ $chq->cheque_number }}</strong></td>
                        <td><strong>₹{{ number_format($chq->amount, 2) }}</strong></td>
                        <td>{{ $chq->cheque_date }}</td>
                        <td>
                            @if($chq->status === 'pending')
                                <span class="badge badge-warning">Pending Clearing</span>
                            @elseif($chq->status === 'cleared')
                                <span class="badge badge-success">Cleared</span>
                            @else
                                <span class="badge badge-danger">Bounced</span>
                            @endif
                        </td>
                        <td>
                            @if($chq->status === 'pending')
                                <div style="display:flex; gap:6px;">
                                    <form method="POST" action="{{ route('school.fees.pending-cheques') }}">
                                        @csrf
                                        <input type="hidden" name="cheque_id" value="{{ $chq->id }}">
                                        <input type="hidden" name="action" value="clear">
                                        <button type="submit" class="btn btn-success" style="padding:4px 8px; font-size:11px;">
                                            <i class="fas fa-check"></i> Clear
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('school.fees.pending-cheques') }}">
                                        @csrf
                                        <input type="hidden" name="cheque_id" value="{{ $chq->id }}">
                                        <input type="hidden" name="action" value="bounce">
                                        <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:11px;">
                                            <i class="fas fa-times"></i> Bounce
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span style="color:var(--t3); font-size:11px;">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px; color:var(--t3);">No cheques recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
